<?php
/**
 * Created by PhpStorm.
 * User: Hoda
 * Date: 28/01/2020
 * Time: 10:22 AM
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class GithubController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login GitHub
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    CONST CACHE_KEY = 'GITHUBCONTROLLER';
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    /**
     * redirectToProvider
     * @param $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * handleProviderCallback
     * @param $provider
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     * @throws \Exception
     */
    public function handleProviderCallback($provider) {
        $list =[];

        // Get the info from githubApi
        $userGitInfo = Socialite::driver($provider)->user();

        // Check user already exist or no
        $user = User::where('provider_id', $userGitInfo->getId())->first();
        if(!$user){
            // add user to database
            $user = User::create([
                'name'        => $userGitInfo->getNickname(),
                'email'       => $userGitInfo->getEmail(),
                'provider_id' => $userGitInfo->getId(),
            ]);
        }

       Auth::login($user, true);

        // Get the repository info
        $dataRepos = $this->getUserRepos($userGitInfo);
        foreach ($dataRepos as $dataRepo) {
            $list[] = $dataRepo->name;
        }

        // Set the repository in lifetime cache
        $key = "handleProviderCallback.{$provider}";
        $cacheKey = $this->getCacheKey($key);

        return cache()->remember($cacheKey, Carbon::now()->addDay(1),function()use($list,$provider){
           return (string) view('gitRepo/index')->with('repoLists',$list);
        });
    }

    /**
     * getUserRepos
     * @param $user
     * @return bool|mixed|string
     */
    private function getUserRepos($user)
    {
        $name=$user->getNickname();
        $token=$user->token;

        // We generate the url for curl
        $curl_url = 'https://api.github.com/users/' .$name. '/repos';

        // We generate the header part for the token
        $curl_token_auth = 'Authorization: token ' . $token;

        // We make the actual curl initialization
        $ch = curl_init($curl_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // We set the right headers: any user agent type, and then the custom token header part that we generated
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: Awesome-Octocat-App', $curl_token_auth));

        // We execute the curl
        $output = curl_exec($ch);

        // And we make sure we close the curl
        curl_close($ch);

        // Then we decode the output and we could do whatever we want with it
        $output = json_decode($output);

        return $output;
    }

    /**
     * getCacheKey
     * @param $key
     * @return string
     */
    public function getCacheKey($key) {
        $key = strtoupper($key);
        return self::CACHE_KEY. ".$key";
    }



}

