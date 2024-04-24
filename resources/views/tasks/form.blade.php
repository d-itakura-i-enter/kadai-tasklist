@if (Auth::id() == $user->id)
    <div class="mt-4">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            
            <div class="form-control mt-4">
                <label for="status" class="label">
                        <span class="label-text">状態:</span>
                </label>
                <textarea rows="2" name="status" class="input input-bordered w-full"></textarea>
            </div>
            
            <div class="form-control mt-4">
                <label for="content" class="label">
                        <span class="label-text">タスク:</span>
                </label>
                <textarea rows="2" name="content" class="input input-bordered w-full"></textarea>
            </div>
        
            <button type="submit" class="btn btn-primary btn-block normal-case">Post</button>
        </form>
    </div>
@endif