<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<script>
  $(document).ready(function() {
      console.log(window.location.href.replace("flush","grid"));
  $.get(window.location.href.replace("flush","grid"), function(data) {
    $("body").html(data);
    console.log("page was loaded");
  });
});
</script>
<html>
  <body>
      <h1>Loading...</h1>
    <img height=100% src="http://i.imgur.com/EATfJf4.gif">
  </body>
</html>