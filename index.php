<?php
    ini_set('display_errors', 1);
       ini_set('display_startup_errors', 1);
       error_reporting(E_ALL);
include('/Applications/MAMP/htdocs/Project1/classes/DB.php');
include('/Applications/MAMP/htdocs/Project1/classes/Login.php');
include('/Applications/MAMP/htdocs/Project1/classes/Post.php');
include('/Applications/MAMP/htdocs/Project1/classes/Comment.php');
include('/Applications/MAMP/htdocs/Project1/classes/Mail.php');
    $showTimeline=False;
    if(Login::isLoggedIn())
    {
        $userid=Login::isLoggedIn();
        $showTimeline=True;
    }
    else
    {
        die('Not Logged In');
    }
    
    if(isset($_GET['postid']))
    {
        Post::likePost($_GET['postid'],$userid);
    }
    if(isset($_POST['comment']))
    {
        Comment::createComment($_POST['commentbody'],$_GET['postid'],$userid);
    }
    
    if(isset($_POST['searchbox']))
    {
        $tosearch=explode(" ",$_POST['searchbox']);
        if(count($tosearch)==1)
        {
            $tosearch=str_split($tosearch[0],2);
        }
        $whereclause="";
        $paramsarray=array(':username'=>'%'.$_POST['searchbox'].'%');
        for($i=0;$i<count($tosearch);$i++)
        {
            $whereclause.="OR username LIKE :u$i ";
            $paramsarray[":u$i"]=$tosearch[$i];
        }
        $users=DB::query('SELECT users.username FROM users WHERE users.username LIKE :username '.$whereclause.'',$paramsarray);
        print_r($users);
        
        $whereclause="";
        $paramsarray=array(':body'=>'%'.$_POST['searchbox'].'%');
        for($i=0;$i<count($tosearch);$i++)
        {
            if($i % 2)
            {
                $whereclause.="OR body LIKE :p$i ";
                $paramsarray[":p$i"]=$tosearch[$i];
            }
        }
        
        $posts=DB::query('SELECT posts.body FROM posts WHERE posts.body LIKE :body '.$whereclause.'',$paramsarray);
        print_r($posts);
    }
    
?>

<!--<form action="index.php" method="post">
<input type="text" name="searchbox" valeu="">
<input type="submit" name="search" value="Search">
</form>-->

<?php
    
    $followingposts=DB::query('SELECT posts.id,posts.body,posts.likes,users.username FROM users,posts,followers WHERE posts.user_id=followers.user_id AND users.id=posts.user_id AND follower_id=:userid ORDER BY posts.likes DESC',array(':userid'=>$userid));
    foreach($followingposts as $post)
    {
            //echo $post['body']." ~ ".$post['username'];
            echo "<!--<form action='index.php?postid=".$post['id']."' method='post'>";
        if(!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$post['id'],':userid'=>$userid)))
            {
            echo "<input type='submit' name='like' value='Like'>";
            }
        else
        {
            echo "<input type='submit' name='unlike' value='Unlike'>";
        }
            echo "<span> ".$post['likes']." likes</span>
        </form>
        <form action='index.php?postid=".$post['id']."' method='post'>
        <textarea name='commentbody' rows='3' cols='50'></textarea>
        <input type='submit' name='comment' value='Comment'>
            </form>-->";
        //Comment::displayComments($post['id']);
        //echo "
            //<hr /></br />";
    }
    $username=DB::query('SELECT username FROM users WHERE id=:userid',array(':userid'=>$userid))[0]['username'];
?>



<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/Footer-Dark.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.css">
    <link rel="stylesheet" href="assets/css/Login-Form-Clean.css">
    <link rel="stylesheet" href="assets/css/Navigation-Clean1.css">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="assets/css/untitled.css">
</head>

<body>
    <header class="hidden-sm hidden-md hidden-lg">
        <div class="searchbox">
            <form>
                <h1 class="text-left">Social Network</h1>
                <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                    <input class="form-control sbox" type="text">
                    <ul class="list-group autocomplete" style="position:absolute;width:100%; z-index: 100">
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false" type="button">MENU <span class="caret"></span></button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                        <li role="presentation"><a href="#">My Profile</a></li>
                        <li class="divider" role="presentation"></li>
                        <li role="presentation"><a href="#">Timeline </a></li>
                        <li role="presentation"><a href="#">Messages </a></li>
                        <li role="presentation"><a href="#">Notifications </a></li>
                        <li role="presentation"><a href="#">My Account</a></li>
                        <li role="presentation"><a href="#">Logout </a></li>
                    </ul>
                </div>
            </form>
        </div>
        <hr>
    </header>
    <div>
        <nav class="navbar navbar-default hidden-xs navigation-clean">
            <div class="container">
                <div class="navbar-header"><a class="navbar-brand navbar-link" href="#"><i class="icon ion-ios-navigate"></i></a>
                    <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
                </div>
                <div class="collapse navbar-collapse" id="navcol-1">
                    <form class="navbar-form navbar-left">
                        <div class="searchbox"><i class="glyphicon glyphicon-search"></i>
                            <input class="form-control sbox" type="text">
                            <ul class="list-group autocomplete" style="position:absolute;width:100%; z-index:100">
                            </ul>
                        </div>
                    </form>
                    <ul class="nav navbar-nav hidden-md hidden-lg navbar-right">
                        <li role="presentation"><a href="#">My Timeline</a></li>
                        <li class="dropdown open"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" href="#">User <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li role="presentation"><a href="profile.php?username=<?php echo $username; ?>">My Profile</a></li>
                                <li class="divider" role="presentation"></li>
                                <li role="presentation"><a href="#">Timeline </a></li>
                                <li role="presentation"><a href="#">Messages </a></li>
                                <li role="presentation"><a href="#">Notifications </a></li>
                                <li role="presentation"><a href="#">My Account</a></li>
                                <li role="presentation"><a href="#">Logout </a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav hidden-xs hidden-sm navbar-right">
                        <li class="active" role="presentation"><a href="#">Timeline</a></li>
                        <li role="presentation"><a href="#">Messages</a></li>
                        <li role="presentation"><a href="#">Notifications</a></li>
                        <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" href="#">User <span class="caret"></span></a>
                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                <li role="presentation"><a href="profile.php?username=<?php echo $username; ?>">My Profile</a></li>
                                <li class="divider" role="presentation"></li>
                                <li role="presentation"><a href="#">Timeline </a></li>
                                <li role="presentation"><a href="#">Messages </a></li>
                                <li role="presentation"><a href="#">Notifications </a></li>
                                <li role="presentation"><a href="#">My Account</a></li>
                                <li role="presentation"><a href="#">Logout </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
    <div class="container">
        <h1>Timeline </h1>
        <div class="timelineposts">

        </div>
    </div>
    <div class="modal fade" role="dialog" tabindex="-1" style="padding-top:100px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title">Comments</h4></div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto">
                    <p>The content of your modal.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" type="button" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-dark navbar-fixed-bottom" style="position:relative">
        <footer>
            <div class="container">
                <p class="copyright">Social Network© 2016</p>
            </div>
        </footer>
    </div>
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/bs-animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.1.1/aos.js"></script>
    <script type="text/javascript">

        
        var start = 5;
        var working = false;
        $(window).scroll(function() {
                if ($(this).scrollTop() + 1 >= $('body').height() - $(window).height()) {
                        if (working == false) {
                                working = true;
                                $.ajax({

                                        type: "GET",
                                        url: "api/posts&start="+start,
                                        processData: false,
                                        contentType: "application/json",
                                        data: '',
                                        success: function(r) {
                                                var posts = JSON.parse(r)
                                                $.each(posts, function(index) {

                                                        if (posts[index].PostImage == "") {

                                                                $('.timelineposts').html(
                                                                        $('.timelineposts').html() +

                                                                        '<li class="list-group-item" id="'+posts[index].PostId+'"><blockquote><p>'+posts[index].PostBody+'</p><footer>Posted by '+posts[index].PostedBy+' on '+posts[index].PostDate+'<button class="btn btn-default" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-flash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Comments</span></button></footer></blockquote></li>'
                                                                )
                                                        } else {
                                                                $('.timelineposts').html(
                                                                        $('.timelineposts').html() +

                                                                        '<li class="list-group-item" id="'+posts[index].PostId+'"><blockquote><p>'+posts[index].PostBody+'</p><img src="" data-tempsrc="'+posts[index].PostImage+'" class="postimg" id="img'+posts[index].postId+'"><footer>Posted by '+posts[index].PostedBy+' on '+posts[index].PostDate+'<button class="btn btn-default" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-flash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Comments</span></button></footer></blockquote></li>'
                                                                )
                                                        }

                                                        $('[data-postid]').click(function() {
                                                                var buttonid = $(this).attr('data-postid');

                                                                $.ajax({

                                                                        type: "GET",
                                                                        url: "api/comments?postid=" + $(this).attr('data-postid'),
                                                                        processData: false,
                                                                        contentType: "application/json",
                                                                        data: '',
                                                                        success: function(r) {
                                                                                var res = JSON.parse(r)
                                                                                showCommentsModal(res);
                                                                        },
                                                                        error: function(r) {
                                                                                console.log(r)
                                                                        }

                                                                });
                                                        });

                                                        $('[data-id]').click(function() {
                                                                var buttonid = $(this).attr('data-id');
                                                                $.ajax({

                                                                        type: "POST",
                                                                        url: "api/likes?id=" + $(this).attr('data-id'),
                                                                        processData: false,
                                                                        contentType: "application/json",
                                                                        data: '',
                                                                        success: function(r) {
                                                                                var res = JSON.parse(r)
                                                                                $("[data-id='"+buttonid+"']").html(' <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+res.Likes+' Likes</span>')
                                                                        },
                                                                        error: function(r) {
                                                                                console.log(r)
                                                                        }

                                                                });
                                                        })
                                                })

                                                $('.postimg').each(function() {
                                                        this.src=$(this).attr('data-tempsrc')
                                                        this.onload = function() {
                                                                this.style.opacity = '1';
                                                        }
                                                })

                                                scrollToAnchor(location.hash)

                                                start+=5;
                                                setTimeout(function() {
                                                        working = false;
                                                }, 4000)

                                        },
                                        error: function(r) {
                                                console.log(r)
                                        }

                                });
                        }
                }
        })

        
        
        
        $(document).ready(function() {

                $('.sbox').keyup(function() {
                        $('.autocomplete').html("")
                        $.ajax({

                                type: "GET",
                                url: "api/search1?query=" + $(this).val(),
                                processData: false,
                                contentType: "application/json",
                                data: '',
                                success: function(r) {
                                        r = JSON.parse(r)
                                        for (var i = 0; i < r.length; i++) {
                                                console.log(r[i].body)
                                                $('.autocomplete').html(
                                                        $('.autocomplete').html() +
                                                                        '<a href="profile.php?username='+r[i].username+"#"+r[i].id+'"><li class="list-group-item"><span>'+r[i].body+'</span></li>'
                                                )
                                        }
                                },
                                error: function(r) {
                                        console.log(r)
                                }
                        })
                })
                          
                $('.sbox').keyup(function() {
                        $('.autocomplete').html("")
                        $.ajax({

                                type: "GET",
                                url: "api/search2?query=" + $(this).val(),
                                processData: false,
                                contentType: "application/json",
                                data: '',
                                success: function(r) {
                                        r = JSON.parse(r)
                                        for (var i = 0; i < r.length; i++) {
                                                console.log(r[i].username)
                                                $('.autocomplete').html(
                                                        $('.autocomplete').html() +
                                                                        '<a href="profile.php?username='+r[i].username+'"><li class="list-group-item"><span>'+r[i].username+'</span></li>'
                                                )
                                        }
                                },
                                error: function(r) {
                                        console.log(r)
                                }
                        })
                })

                $.ajax({

                        type: "GET",
                        url: "api/posts&start=0",
                        processData: false,
                        contentType: "application/json",
                        data: '',
                        success: function(r) {
                                var posts = JSON.parse(r)
                                $.each(posts, function(index) {
                                       if(posts[index].PostImage==null)
                                       {
                                        $('.timelineposts').html(
                                                $('.timelineposts').html() + '<blockquote><p>'+posts[index].PostBody+'</p><footer>Posted by '+posts[index].PostedBy+' on '+posts[index].PostDate+'<button class="btn btn-default" data-id="'+posts[index].PostId+'" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" type="button" data-postid="'+posts[index].PostId+'" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-flash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Comments</span></button></footer></blockquote>'
                                        )
                                       }
                                       else
                                       {
                                       $('.timelineposts').html(
                                               $('.timelineposts').html() +

                                                                '<li class="list-group-item" id="'+posts[index].PostId+'"><blockquote><p>'+posts[index].PostBody+'</p><img src="" data-tempsrc="'+posts[index].PostImage+'" class="postimg" id="img'+posts[index].PostId+'"><footer>Posted by '+posts[index].PostedBy+' on '+posts[index].PostDate+'<button class="btn btn-default" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;" data-id=\"'+posts[index].PostId+'\"> <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+posts[index].Likes+' Likes</span></button><button class="btn btn-default comment" data-postid=\"'+posts[index].PostId+'\" type="button" style="color:#eb3b60;background-image:url(&quot;none&quot;);background-color:transparent;"><i class="glyphicon glyphicon-flash" style="color:#f9d616;"></i><span style="color:#f9d616;"> Comments</span></button></footer></blockquote></li>'
                                       )
                                       }
                                        $('[data-postid]').click(function() {
                                                var buttonid = $(this).attr('data-postid');

                                                $.ajax({

                                                        type: "GET",
                                                        url: "api/comments?postid=" + $(this).attr('data-postid'),
                                                        processData: false,
                                                        contentType: "application/json",
                                                        data: '',
                                                        success: function(r) {
                                                                var res = JSON.parse(r)
                                                                showCommentsModal(res);
                                                        },
                                                        error: function(r) {
                                                                console.log(r)
                                                        }

                                                });
                                        });

                                        $('[data-id]').click(function() {
                                                var buttonid = $(this).attr('data-id');
                                                $.ajax({

                                                        type: "POST",
                                                        url: "api/likes?id=" + $(this).attr('data-id'),
                                                        processData: false,
                                                        contentType: "application/json",
                                                        data: '',
                                                        success: function(r) {
                                                                var res = JSON.parse(r)
                                                                $("[data-id='"+buttonid+"']").html(' <i class="glyphicon glyphicon-heart" data-aos="flip-right"></i><span> '+res.Likes+' Likes</span>')
                                                        },
                                                        error: function(r) {
                                                                console.log(r)
                                                        }

                                                });
                                        })
                                })
                       $('.postimg').each(function()
                       {
                                        this.src=$(this).attr('data-tempsrc')
                                          this.onload=function()
                                          {
                                          this.style.opacity='1';
                                          }
                       })

                        },
                        error: function(r) {
                                console.log(r)
                        }

                });

        });

        function showCommentsModal(res) {
                $('.modal').modal('show')
                var output = "";
                for (var i = 0; i < res.length; i++) {
                        output += res[i].Comment;
                        output += " ~ ";
                        output += res[i].CommentedBy;
                        output += "<hr />";
                }

                $('.modal-body').html(output)
        }

    </script>
</body>

</html>
