<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckLogin;
use App\Models\News;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->CL = new CheckLogin();
        $this->newsmodel = new News();
    }
    public function index()
    {
        try {
            $news = News::get();
            if (count($news) >= 1) {
                return response()->json(['status' => 200, 'message' => '取得所有最新消息成功', 'result' => $news, 'success' => true], 200);
            } else {
                return response()->json(['status' => 202, 'message' => '取得所有最新消息失敗', 'result' => [], 'success' => false], 202);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '取得所有最新消息失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    public function show($id)
    {
        try {
            $news = News::find($id);
            if ($news != null) {
                $news['type'] = $news->newstype->type;
                $news['name'] = $news->user->name;
                return response()->json(['status' => 200, 'message' => '取得最新消息成功', 'result' => $news, 'success' => true], 200);
            } else {
                return response()->json(['status' => 202, 'message' => '取得最新消息失敗', 'result' => [], 'success' => false], 202);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' => '取得最新消息失敗=>' . $th->getMessage(), 'success' => false], 400);
        }
    }
    //admin
    public function store(Request $req)
    {
        try {
            //輸入規則
            $datas = $req->validate(['title' => 'required|string', 'content' => 'required|string', 'news_types_id' => 'required']);
            $news = News::create($datas);
            $token = $req->header('token');
            $id = $this->CL->decodeToken($token);
            $user = User::findOrFail($id);
            $user->news()->save($news);
            return response()->json(['status' => 200, 'message' => $user->name . '發佈' . $news->title . '消息成功', 'success' => true], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => '發佈消息失敗=>' . $e->getMessage(), 'success' => false], 400);
        }
    }
    public function update(Request $req, $id)
    {
        try {
            $news = News::find($id);
            $old_title = $news->title;
            if ($news != null) {
                $datas = $req->validate(['title' => 'required|string', 'content' => 'required|string', 'news_types_id' => 'required']);
                $update = $news->update($datas);
                if ($update) {
                    return response()->json(['status' => 200, 'message' => $old_title . '消息編輯成功', 'success' => true], 200);
                } else {
                    return response()->json(['status' => 202, 'message' => '消息編輯失敗', 'success' => false], 202);
                }
            } else {
                return response()->json(['status' => 400, 'message' => '消息編輯失敗=>沒有這個消息', 'success' => false], 400);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => '消息編輯失敗=>' . $e->getMessage(), 'success' => false], 400);
        }
    }
    public function destroy($id)
    {
        try {
            $news = News::withTrashed()->find($id);
            if ($news != null) {
                $forcedelete = $news->forcedelete();
                if ($forcedelete) {
                    return response()->json(['status' => 200, 'message' =>  '該則消息(' . $news->title . ')已刪除', 'success' => true], 200);
                } else {
                    return response()->json(['status' => 202, 'message' => '不明原因<刪除失敗>', 'success' => false], 202);
                }
            } else {
                return response()->json(['status' => 202, 'message' => '沒有這則消息<刪除失敗>', 'success' => false], 202);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'message' => '消息刪除失敗=>' . $e->getMessage(), 'success' => false], 400);
        }
    }
    public function invisible($id)
    {
        return $this->newsmodel->invisible($id);
    }
    public function recovery($id)
    {
        return $this->newsmodel->recovery($id);
    }
    public function getAllNews()
    {
        try {
            $news = News::withTrashed()->orderBy('id')->get();
            if (count($news) >= 1) {
                return response()->json(['status' => 200, 'message' =>  '取得所有消息成功', 'result' => $news, 'success' => true], 200);
            } else {
                return response()->json(['status' => 202, 'message' =>  '取得所有消息失敗', 'result' => [], 'success' => false], 202);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 400, 'message' =>  '取得所有消息失敗=>' . $th->getMessage(), 'result' => [], 'success' => false], 400);
        }
    }
}
