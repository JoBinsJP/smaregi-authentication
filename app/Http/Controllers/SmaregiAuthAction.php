<?php

namespace App\Http\Controllers;


use App\Infrastructure\Libraries\HttpRequestOption;
use App\Infrastructure\Models\SmaregiContract;
use App\Infrastructure\Models\Store;
use Arr;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Class SmaregiAuthAction
 * @package App\Http\Controllers
 */
class SmaregiAuthAction extends Controller
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * HttpRequest constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function __invoke(Request $request)
    {
        $authorizationCode = $request->input('code');
        [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
        ] = $this->authenticate($authorizationCode);

        // Fetch smaregi user info
        [
            'sub'      => $smaregiId,
            'email'    => $smaregiEmail,
            'contract' => $smaregiContract,
        ] = $this->getUserInfo($accessToken);

        $contractId = Arr::get($smaregiContract, 'id');

        // Fetch system token
        ['access_token' => $appAccessToken] = $this->appAccessToken($contractId);

        // Save smaregi contract
        $contract = $this->saveContract($contractId, $appAccessToken);

        // Save and login user
        $userData = [
            'smaregiId'           => $smaregiId,
            'email'               => $smaregiEmail,
            'smaregiAccessToken'  => $accessToken,
            'smaregiRefreshToken' => $refreshToken,
        ];

        $this->login($contract, $userData);

        // Fetch smaregi stores
        $stores = $this->getStores($appAccessToken, $contract->contract_id);

        // Save Stores
        $this->saveStores($contract, $stores);

        return redirect()->route('dashboard');
    }

    public function authorizeUser(): string
    {
        return redirect(
            "https://id.smaregi.dev/authorize?response_type=code&client_id=".env(
                "SMAREGI_CLIENT_ID"
            )."&scope=openid+email+offline_access"
        );
    }

    public function dashboard()
    {
        $contractId = auth()->user()->smaregi_contract->id;
        $stores     = $this->getAllByContractId($contractId);

        return view('dashboard', compact('stores'));
    }

    // fetch access token
    private function authenticate(string $authorizationCode): array
    {
        $url          = "https://id.smaregi.dev/authorize/token";
        $clientId     = env('SMAREGI_CLIENT_ID');// your client_id
        $clientSecret = env('SMAREGI_CLIENT_SECRET');// your client_secret

        $option = new HttpRequestOption();
        $option->url($url);
        $option->method('POST');
        $option->token(
            [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
            ],
            'Basic'
        );
        //add scopes and grant_type in query
        $formData               = [];
        $formData['grant_type'] = 'authorization_code';
        $formData['code']       = $authorizationCode; // obtained from previous step
        $formData['scope']      = 'pos.transactions:write pos.stores:read pos.products:read';

        $option->formData($formData);

        return $this->request($option);
    }

    // fetch user info
    private function getUserInfo(string $accessToken): array
    {
        $url    = "https://id.smaregi.dev/userinfo";
        $option = new HttpRequestOption();
        $option->url($url);
        $option->token($accessToken);

        return $this->request($option);
    }

    // fetching access token
    private function appAccessToken(string $contractId): array
    {
        $url          = "https://id.smaregi.dev/app/$contractId/token"; // contract_id from previous step
        $clientId     = env('SMAREGI_CLIENT_ID');// your client_id
        $clientSecret = env('SMAREGI_CLIENT_SECRET');// your client_secret

        $option = new HttpRequestOption();
        $option->url($url);
        $option->method('POST');
        $option->token(
            [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
            ],
            'Basic'
        );
        $formData               = [];
        $formData['grant_type'] = 'client_credentials';
        $formData['scope']      = 'pos.transactions:write pos.transactions:read pos.stores:read pos.products:read';
        $option->formData($formData);

        return $this->request($option);
    }

    // save contract
    private function saveContract(string $contractId, string $appAccessToken): SmaregiContract
    {
        return SmaregiContract::updateOrCreate(
            ['contract_id' => $contractId],
            [
                'system_access_token' => $appAccessToken,
            ]
        );
    }

    /**
     * @param SmaregiContract $smaregiContract
     * @param                 $userData
     */
    private function login(SmaregiContract $smaregiContract, $userData)
    {
        $user = $smaregiContract->users()->updateOrCreate(
            [
                'smaregi_contract_id' => $smaregiContract->id,
                'email'               => Arr::get($userData, 'email'),
            ],
            [
                'smaregi_id'            => Arr::get($userData, 'smaregiId'),
                'email'                 => Arr::get($userData, 'email'),
                'smaregi_access_token'  => Arr::get($userData, 'smaregiAccessToken'),
                'smaregi_refresh_token' => Arr::get($userData, 'smaregiRefreshToken'),
                'logged_in_at'          => Carbon::now(),
            ]
        );
        auth()->login($user, true);
    }

    /**
     * @param string $accessToken
     * @param string $contractId
     * @param array  $query
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getStores(string $accessToken, string $contractId, array $query = []): array
    {
        $url = "https://api.smaregi.dev/{$contractId}/pos/stores/";

        $option = new HttpRequestOption();
        $option->url($url);
        $option->token($accessToken);
        $option->query($query);

        return $this->request($option);
    }

    /**
     * @param SmaregiContract $contract
     * @param array           $stores
     *
     * @throws Exception
     */
    private function saveStores(SmaregiContract $contract, array $stores): void
    {
        $this->mapStoresByContract(
            $contract,
            collect($stores)->map(
                function (array $store) {
                    if ( Arr::get($store, 'division') === "2" ) {
                        return null;
                    }

                    return $store;
                }
            )->filter()
        );
    }

    /**
     * @param SmaregiContract $contract
     * @param Collection      $stores
     *
     * @throws Exception
     */
    private function mapStoresByContract(SmaregiContract $contract, Collection $stores): void
    {
        try {
            $stores->map(
                function ($store) use ($contract) {
                    return $this->saveStoreByContract($contract, $store);
                }
            );
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param SmaregiContract $contract
     * @param                 $store
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    private function saveStoreByContract(SmaregiContract $contract, $store)
    {
        return $contract->smaregi_stores()->updateOrCreate(
            [
                'smaregi_contract_id' => $contract->id,
                'smaregi_store_id'    => Arr::get($store, 'storeId'),
            ],
            [
                'smaregi_store_id'   => Arr::get($store, 'storeId'),
                'smaregi_store_name' => Arr::get($store, 'storeName'),
                'is_paused'          => Arr::get($store, 'pauseFlag'),
            ]
        );
    }

    /**
     * @param string $contractId
     *
     * @return Collection
     */
    private function getAllByContractId(string $contractId): Collection
    {
        return Store::where('smaregi_contract_id', $contractId)->orderBy('smaregi_store_id', 'asc')->get();
    }

    /**
     * @param HttpRequestOption $requestOptions
     * @param bool              $handleCommonException
     *
     * @return array|null
     * @throws GuzzleException
     */
    private function request(HttpRequestOption $requestOptions, bool $handleCommonException = true): ?array
    {
        $method  = $requestOptions->method();
        $url     = $requestOptions->url();
        $options = $requestOptions->httpOptions();
        try {
            $resource = $this->client->request($method, $url, $options);
        } catch (GuzzleException $exception) {
            throw $exception;
        }

        return json_decode($resource->getBody()->getContents(), true);
    }
}
