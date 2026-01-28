<!DOCTYPE html>
<html>
<head>
    <title>Mini Facebook</title>
</head>

<body style="background:#f0f2f5; font-family:Arial; margin:0;">

<!-- HEADER -->
<div style="background:#1877f2; padding:25px;">
    <h1 style="color:white; margin:0; text-align:center;">
        Welcome to Mini Facebook
    </h1>
</div>

<div style="width:900px; margin:40px auto;">

  <!-- CREATE POST -->
<div style="background:#fff; padding:20px; border-radius:12px; margin-bottom:25px;">

    <form method="POST" action="/posts">
        @csrf

        <div style="display:flex; gap:15px; align-items:flex-start;">

           

            <!-- TEXTAREA -->
            <textarea
                name="content"
                placeholder="What's on your mind?"
                style="
                    flex:1;
                    height:100px;
                    border-radius:10px;
                    padding:15px;
                    font-size:18px;
                    border:1px solid #ccc;
                    resize:none;
                "
            ></textarea>

        </div>

        <!-- BUTTON ROW -->
        <div style="text-align:right; margin-top:15px;">
            <button
                style="
                    background:#1877f2;
                    color:#fff;
                    padding:10px 22px;
                    border:none;
                    border-radius:8px;
                    font-size:16px;
                    cursor:pointer;
                "
            >
                Post
            </button>
        </div>

    </form>
</div>


    <!-- POSTS -->
   @foreach ($posts as $post)
<div style="
    background:#fff;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
    position:relative;
">

    <!-- TOP RIGHT ACTIONS -->
    <div style="position:absolute; top:15px; right:15px;">
        <button
            onclick="openEdit({{ $post->id }}, '{{ addslashes($post->content) }}')"
            style="margin-right:6px;"
        >
            Edit
        </button>

        <button
            onclick="openDelete({{ $post->id }})"
            style="color:red;"
        >
            Delete
        </button>
    </div>

    <div style="display:flex; gap:15px;">

        <!-- AVATAR -->
        <img
            src="https://scontent.fceb5-1.fna.fbcdn.net/v/t39.30808-6/353850315_101035759700191_5622010997355588825_n.jpg?_nc_cat=110&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeEu5Wjz8zMMw5YjzyarD_6hw2ys3bEhMNvDbKzdsSEw23WEv-_ivSdaObYcQJgEtG1S0vUqe9nWilp2F52dIRY-&_nc_ohc=SbBzYh9qgLIQ7kNvwFMeJ4R&_nc_oc=Adn8V7HddblNqNm4xLecQRyQXv_H_xelpwJbRueoZbTMh8pmGL9ld6L0UdkS0UUVdgQ&_nc_zt=23&_nc_ht=scontent.fceb5-1.fna&_nc_gid=XxC20m9zs3axlRVZrvjOQQ&oh=00_AfowG1_gbMu_auueC_woz9Z7WPBttVINSpZjWtEv5cCdEg&oe=697E39F6"
            style="width:70px;height:70px;border-radius:50%;object-fit:cover;"
        >

        <div style="flex:1;">
            <p style="font-size:20px; margin:0 0 6px;">
                {{ $post->content }}
            </p>

            <small style="color:gray;">
                {{ $post->created_at->diffForHumans() }}
            </small>
        </div>
    </div>

    <!-- DIVIDER -->
    <hr style="margin:15px 0;">

    <!-- ACTION BUTTONS -->
    <div style="
        display:flex;
        justify-content:space-around;
        font-size:16px;
    ">

        <button style="background:none;border:none;cursor:pointer;">
            👍 Like
        </button>

        <button style="background:none;border:none;cursor:pointer;">
            💬 Comment
        </button>

        <button style="background:none;border:none;cursor:pointer;">
            🔁 Share
        </button>

    </div>

</div>
@endforeach

</div>

<!-- ================= DELETE MODAL ================= -->
<div id="deleteModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);">
    <div style="background:#fff; width:420px; margin:200px auto; padding:30px; border-radius:12px; text-align:center;">
        <p style="font-size:20px;">Are you sure you want to delete this post?</p>

        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')

            <button
                type="button"
                onclick="closeDelete()"
                style="padding:10px 20px; font-size:16px;"
            >
                No
            </button>

            <button
                style="padding:10px 20px; font-size:16px; color:red; margin-left:10px;"
            >
                Yes
            </button>
        </form>
    </div>
</div>

<!-- ================= EDIT MODAL ================= -->
<div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5);">
    <div style="background:#fff; width:600px; margin:150px auto; padding:30px; border-radius:12px;">
        <h2>Edit Post</h2>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')

            <textarea
                name="content"
                id="editContent"
                style="
                    width:100%;
                    height:160px;
                    font-size:18px;
                    padding:15px;
                "
            ></textarea>

            <br><br>

            <button
                type="button"
                onclick="closeEdit()"
                style="padding:10px 20px; font-size:16px;"
            >
                Cancel
            </button>

            <button
                style="padding:10px 20px; font-size:16px; margin-left:10px;"
            >
                Update
            </button>
        </form>
    </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
    function openDelete(id) {
        document.getElementById('deleteForm').action = '/posts/' + id;
        document.getElementById('deleteModal').style.display = 'block';
    }

    function closeDelete() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    function openEdit(id, content) {
        document.getElementById('editForm').action = '/posts/' + id;
        document.getElementById('editContent').value = content;
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEdit() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>

</body>
</html>
