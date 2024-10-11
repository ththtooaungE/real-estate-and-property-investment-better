<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdvertisementResource;
use App\Http\Resources\BlogResource;
use App\Http\Resources\PostResource;
use App\Models\Advertisement;
use App\Models\Blog;
use App\Models\Post;
use App\Traits\ResponseFormattable;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    use ResponseFormattable;

    public function home()
    {
        $data = [];
        $data['admin_posts_sell'] = PostResource::collection(Post::filter(['is_admin' => true, 'status' => 'sell'])->take(4)->get());
        $data['admin_posts_rent'] = PostResource::collection(Post::filter(['is_admin' => true, 'status' => 'rent'])->take(4)->get());

        $data['agent_posts_sell'] = PostResource::collection(Post::filter(['is_admin' => false, 'status' => 'sell'])->take(4)->get());
        $data['agent_posts_rent'] = PostResource::collection(Post::filter(['is_admin' => false, 'status' => 'rent'])->take(4)->get());


        $data['boosted_posts_sell'] = PostResource::collection(Post::boosted()->filter(['status' => 'sell'])->limit(4)->get());
        $data['boosted_posts_rent'] = PostResource::collection(Post::boosted()->filter(['status' => 'rent'])->limit(4)->get());

        $data['advertisements'] = AdvertisementResource::collection(Advertisement::filter(['is_active' => true])->get());

        $rentPostIds = $data['boosted_posts_rent']->pluck('id')->toArray();        // Extract the post IDs from the results
        $sellPostIds = $data['boosted_posts_sell']->pluck('id')->toArray();        // Extract the post IDs from the results
        $postIds = array_merge($rentPostIds, $sellPostIds);

        DB::table('boosts')->whereIn('post_id', $postIds)->increment('equalizer');        // Update the equalizer for all boosts associated with the retrieved post IDs

        $data['blogs'] = BlogResource::collection(Blog::latest('id')->take(4)->get());

        return $this->successResponse('success', 'Successfully retrived', 200, $data);
    }

    public function boostEqualizer(array $ids)
    {
        DB::table('boosts')->whereIn('post_id', $ids)->increment('equalizer');        // Update the equalizer for all boosts associated with the retrieved post IDs
    }
}
