<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;    // 追加

class TasksController extends Controller
{
    // タスク一覧表示画面
    public function index()
    {
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザーを取得
            $user = \Auth::user();
            // ユーザーの投稿の一覧を作成日時の降順で取得
            // （後のChapterで他ユーザーの投稿も取得するように変更しますが、現時点ではこのユーザーの投稿のみ取得します）
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
            // タスク一覧ビューでそれを表示
            return view('tasks.index', [     // 追加
            'tasks' => $tasks,        // 追加
            ]); 
        }else{
             return view('dashboard');
        }
        
        
    }

    // getでtasks/createにアクセスされた場合の「新規登録画面表示処理
    public function create()
    {
        if (\Auth::check()) { // 認証済みの場合
        $task = new Task;
        
        // タスク作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
        }else{
            return view('dashboard');
        }
    }

    // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        if (\Auth::check()) 
        {
            // バリデーション
            $request->validate([
                'status' => 'required|max:10',
                'content' => 'required|max:255',
            ]);
            // 認証済みユーザー（閲覧者）の投稿として作成（リクエストされた値をもとに作成）
            $request->user()->tasks()->create([
                'content' => $request->content,
                'status' => $request->status,
                // ['content' => $request->content],
                // ['status' => $request->status],
            ]);
            
            // 前のURLへリダイレクトさせる
            return redirect('/');
        }else{
            return view('dashboard');
        }
    }

    // getでtasks/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        if (\Auth::check()){ 
            // idの値でメッセージを検索して取得
            $task = Task::findOrFail($id);
    
            // メッセージ詳細ビューでそれを表示
            return view('tasks.show', [
                'task' => $task,
            ]);
        }else{
            return view('dashboard');
        }
    }

    // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        if (\Auth::check()){ 
            // idの値でメッセージを検索して取得
            $task = Task::findOrFail($id);
    
            // メッセージ編集ビューでそれを表示
            return view('tasks.edit', [
                'task' => $task,
            ]);
        }else{
            return view('dashboard');
        }
    }

    // putまたはpatchでtasks/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        if (\Auth::check()){ 
            // バリデーション
            $request->validate([
                'status' => 'required|max:10',
                'content' => 'required|max:255',
            ]);
            // idの値でメッセージを検索して取得
            $task = Task::findOrFail($id);
            // メッセージを更新
            $task->status = $request->status;
            $task->content = $request->content;
            $task->save();
    
            // トップページへリダイレクトさせる
            return redirect('/');
        }else{
            return view('dashboard');
        }
    }

    // deleteでtasks/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        // idの値で投稿を検索して取得
        $task = Task::findOrFail($id);
        
        // 認証済みユーザー（閲覧者）がその投稿の所有者である場合は投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
            return back()
                ->with('success','Delete Successful');
        }else{
            return view('dashboard');
        }
    }
}
