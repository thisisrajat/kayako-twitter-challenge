<!DOCTYPE html>
  <head>
  <title>Kayako Twitter Custom Searcher</title>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.4/semantic.min.css">
  <script src="https://code.jquery.com/jquery-2.2.4.min.js"><script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.4/semantic.min.js"></script>
  <style>
    .btn-grp {
      left: calc((100vw - 467px) / 2) !important;
      top: 4%;
      position: absolute;
    }
    .data-dump {
      font-family: monospace;
      line-height: 20px;
      margin-top: 10%;
      overflow: scroll;
    }
  </style>
  <script type="text/javascript">
    $(document).ready(function(){
      $('#search').on('click', function() {
        var hashtag = $("#hashtag").val();
        var rt = $("#retweetCount").val();
        if(hashtag === '') {
          hashtag = '#custserv';
        }
        $("#search").addClass('loading');
        $.get({
          contentType: "application/json; charset=utf-8",
          url: "src/kayako_wrapper.php",
          data: {retweetCount: encodeURIComponent(rt), query: encodeURIComponent(hashtag)},
          success: function(res) {
            $("#search").removeClass('loading');
            $(".data-dump").html(JSON.stringify(JSON.parse(res), null, 4));
          },
          error: function(res) {
            $("#search").removeClass('loading');
          }
        });
      });
    });
  </script>
  </head>
  <body>
  <div class="ui container btn-grp">
    <div class="ui labeled input ">
      <div class="ui label">
        #
      </div>
      <input type="text" placeholder="Hashtag" id="hashtag">
    </div>
    <div class="ui input">
      <input type="text" placeholder="Retweet Count" id="retweetCount">
    </div>
    <div class="ui animated button primary" tabindex="0" id="search">
      <div class="visible content">Search</div>
      <div class="hidden content">
        <i class="right arrow icon"></i>
      </div>
    </div>
  </div>
  <div class="ui container">
    <pre class="data-dump">
    </pre>
  </div>
  </body>
</html>