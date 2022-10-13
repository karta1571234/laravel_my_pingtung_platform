<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'content', 'news_types_id', 'img_url'];
    protected $hidden = ['deleted_at'];

    //relation
    public function newstype()
    {
        return $this->belongsTo('App\Models\NewsType', 'news_types_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    //function
    public function invisible($id)
    {
        try {
            $news = News::withTrashed()->find($id);
            if ($news != null) {
                if ($news->deleted_at == null) {
                    $status = $news->delete();
                    if ($status) {
                        return response()->json(['status' => 200, 'message' => '該則消息(' . $news->title . ')已隱藏', 'success' => true], 200);
                    } else {
                        return response()->json(['status' => 202, 'message' => '不明原因<隱藏失敗>', 'success' => false], 202);
                    }
                } else {
                    return response()->json(['status' => 202, 'message' => $news->title . '已經被隱藏了', 'success' => false], 202);
                }
            } else {
                return response()->json(['status' => 202, 'message' => '沒有這個消息<隱藏失敗>', 'success' => false], 202);
            }
        } catch (Exception $th) {
            return response()->json(['status' => 400, 'message' => '消息隱藏失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function recovery($id)
    {
        try {
            $news = News::withTrashed()->find($id);
            if ($news != null) {
                if ($news->deleted_at != null) {
                    $status = $news->restore();
                    if ($status) {
                        return response()->json(['status' => 200, 'message' =>  '該則消息(' . $news->title . ')已可見', 'success' => true], 200);
                    }
                    return response()->json(['status' => 202, 'message' => '不明原因<可見失敗>', 'success' => false], 202);
                }
                return response()->json(['status' => 202, 'message' => $news->title . '不需可見<沒有隱藏>', 'success' => false], 202);
            }
            return response()->json(['status' => 202, 'message' => '沒有這則消息<可見失敗>', 'success' => false], 202);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => '消息可見失敗=>' . $e->getMessage(), 'success' => false], 400);
        }
    }
}
