<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
</head>
<body style="background:#f0f2f5; font-family:Arial">

<div style="width:500px; margin:40px auto; background:#fff; padding:20px; border-radius:8px;">
    <h3>Edit Post</h3>

    <form method="POST" action="/posts/{{ $post->id }}">
        @csrf
        @method('PUT')

        <textarea name="content"
                  style="width:100%; height:100px; padding:10px;">{{ $post->content }}</textarea>

        <br><br>

        <button style="background:#1877f2; color:#fff; border:none; padding:8px 15px; border-radius:6px;">
            Update
        </button>

        <a href="/" style="margin-left:10px;">Cancel</a>
    </form>
</div>

</body>
</html>
