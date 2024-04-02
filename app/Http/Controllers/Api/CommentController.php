<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Clinic;
use App\Models\Comment;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function confirmation(Comment $comment)
    {

            $comment->update([
                'status'=>'confirmed'
            ]);
            return response()->json([
                'status'=>false,
                'message'=>'comment confirmed successfully'
            ]);

    }
    public function delete(Comment $comment)
    {
        $comment->delete();
        return response()->json([
           'status'=>true,
           'message'=>'comment delete successfully'
        ]);
    }
    public function table()
    {
        $comments=DB::table('comments')
            ->select('comments.score','comments.subject','comments.status',DB::raw('DATE(comments.created_at) as created_date'),'comments.content','users.first_name','users.last_name','users.phone_number')
            ->where('comments.deleted_at',NULL)
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->get();

        return response()->json([
           'status'=>true,
           'message'=> $comments
        ]);
    }

    public function serviceComment(Request $request)
    {
        $service=Service::find($request->service_id);
        $service->comments()->create([
           'content'=>$request->content1 ,
           'subject'=>'service',
           'score'=>$request->score ,
            'user_id'=>$request->user_id
        ]);
        return response()->json([
           'status'=>true,
           'message'=>'service comment created successfully'
        ]);
    }

    public function serviceShow()
    {
        $service_comments=DB::table('comments')
            ->select('comments.score','comments.content','users.first_name','users.last_name')
            ->where(['comments.subject'=>'service','comments.deleted_at'=>NULL,'comments.status'=>'confirmed'])
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->get();
        return response()->json([
            'status'=>true,
            'message'=>$service_comments
        ]);
    }
    public function clinicComment(Request $request)
    {
        $clinic=Clinic::find($request->clinic_id);
        $clinic->comments()->create([
           'content'=>$request->content1 ,
           'subject'=>'clinic',
           'score'=>$request->score ,
            'user_id'=>$request->user_id
        ]);
        return response()->json([
           'status'=>true,
           'message'=>'clinic comment created successfully'
        ]);
    }

    public function clinicShow()
    {
        $clinic_comments=DB::table('comments')
            ->select('comments.score','comments.content','users.first_name','users.last_name')
            ->where(['comments.subject'=>'clinic','comments.deleted_at'=>NULL,'comments.status'=>'confirmed'])
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->get();
        return response()->json([
            'status'=>true,
            'message'=>$clinic_comments
        ]);
    }
    public function productComment(Request $request)
    {
        $product=Product::find($request->product_id);
        $product->comments()->create([
           'content'=>$request->content1 ,
           'subject'=>'product',
           'score'=>$request->score ,
            'user_id'=>$request->user_id
        ]);
        return response()->json([
           'status'=>true,
           'message'=>'product comment created successfully'
        ]);
    }
    public function productShow()
    {
        $product_comments=DB::table('comments')
            ->select('comments.score','comments.content','users.first_name','users.last_name')
            ->where(['comments.subject'=>'product','comments.deleted_at'=>NULL,'comments.status'=>'confirmed'])
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->get();
        return response()->json([
            'status'=>true,
            'message'=>$product_comments
        ]);
    }

    public function articleComment(Request $request)
    {
        $artticle=Article::find($request->artticle_id);
        $artticle->comments()->create([
           'content'=>$request->content1 ,
           'subject'=>'artticle',
           'score'=>$request->score ,
            'user_id'=>$request->user_id
        ]);
        return response()->json([
           'status'=>true,
           'message'=>'artticle comment created successfully'
        ]);
    }
    public function articleShow()
    {
        $article_comments=DB::table('comments')
            ->select('comments.score','comments.content','users.first_name','users.last_name')
            ->where(['comments.subject'=>'article','comments.deleted_at'=>NULL,'comments.status'=>'confirmed'])
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->get();
        return response()->json([
            'status'=>true,
            'message'=>$article_comments
        ]);
    }
}
