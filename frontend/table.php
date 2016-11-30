<?php
$RESULTS_PER_PAGE=3000;

require 'dbCreds.php';
function URLstrip($arg)
{
$end= preg_replace('~(&?)'.$arg.'=[^&]*~','',explode('?', $_SERVER['REQUEST_URI'], 2)[1]);
return 'http://' . $_SERVER['HTTP_HOST'] .explode('?',$_SERVER['REQUEST_URI'], 2)[0]."?". $end;
}

$page=1;
if (isset($_GET['page'])) { //search term
    $page = intval($_GET['page']);
}

if (isset($_GET['s'])) { //search term
    $s = $_GET['s'];
    $search_term=$s;
}
else{
    $s="";
    $search_term="Search...";
}

    $sql="SELECT DISTINCT e.id as ID,e.name as Name, e.volume as Volume,CAST(e.proof AS UNSIGNED) as Proof ,e.type as Type,
        s1.the_min AS Price,
        s1.price_per_liter as PPV,
        s1.price_per_abv as PPABV,
        s1.perc_diff as `PDM`,
        s1.avg_dif_perc as `PDA`
        FROM Items e
          INNER JOIN CurrentPrices s1 ON s1.id=e.id
        WHERE (e.name LIKE '%$s%' OR e.id Like '%$s%');";

// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
$result = mysqli_query($conn,$sql);
$all_property = array();  //declare an array for saving property
$table_str='<table id="table" class="pager tablesorter">
        <thead><tr class="data-heading">';  //initialize table tag
while ($property = mysqli_fetch_field($result)) {
    $tool_array=array("PPV"=>"Price Per Liter", "PPABV"=>"Price Per Liter of pure alchol", "PDM"=>"Percent Change since Last Month", "PDA"=>"Percent Change from Average Price");
    
    if($property->name=="Volume" || $property->name=="Type"|| $property->name=="Category")
    {
        $table_str=$table_str.'<th class="filter-select filter-exact" data-placeholder="Fiter"><b>' . $property->name . '</b>';
    }
    elseif($property->name=="Proof"){
        $table_str=$table_str.'<th class="total"<b>' . $property->name . '</b>';
    }
    
    else
    {
        $table_str=$table_str.'<th><b>' . $property->name . '</b>';
    }
    
    if(!is_null($tool_array[$property->name]))
    {
        $table_str=$table_str.'<a style="text-decoration:none" data-toggle="tooltip" title="'.$tool_array[$property->name].'"> <span class="glyphicon glyphicon-question-sign"></span></a>';
    }
    $table_str=$table_str.'</th>';  //get field name for header
    
    array_push($all_property, $property->name);  //save those to array
}
$table_str=$table_str. '</tr></thead> <tfoot>
    
    <tr>
      <th colspan="13" class="ts-pager form-horizontal">
        <button type="button" class="btn first"><i class="icon-step-backward glyphicon glyphicon-step-backward"></i></button>
        <button type="button" class="btn prev"><i class="icon-arrow-left glyphicon glyphicon-backward"></i></button>
        <span class="pagedisplay"></span> <!-- this can be any element, including an input -->
        <button type="button" class="btn next"><i class="icon-arrow-right glyphicon glyphicon-forward"></i></button>
        <button type="button" class="btn last"><i class="icon-step-forward glyphicon glyphicon-step-forward"></i></button>
        <select class="pagesize input-mini" title="Select page size">
          <option selected="selected" value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
          <option value="1000">1000</option>
        </select>
        <select class="pagenum input-mini" title="Select page number"></select>
      </th>
    </tr>
  </tfoot><tbody>'; //end tr tag

//showing all data
while ($row = mysqli_fetch_array($result)) {
    $table_str=$table_str. "<tr>";
    foreach ($all_property as $item) {
        $table_str=$table_str. '<td>';
        if ($item=="Name")
        {
            $table_str=$table_str. '<a target="_blank" href="product.php?id='.$row[0].'">';
        }
        elseif($item=="Price")
        {
            $table_str=$table_str."$";
        }
            $table_str=$table_str. $row[$item];  //get items using property value
        if ($item=="Name")
        {
            $table_str=$table_str. '</a>';
        }
        if ($item=="Name" && is_null($row["PDM"]))
        {
            $table_str=$table_str." <span class=\"label label-warning\">NEW</span>";
        }
        
           $table_str=$table_str. '</td>';
    }
    //$table_str=$table_str. "<td><a target='_blank' href='product.php?id=".$row[0]."'>Product</a></td>";
    $table_str=$table_str. '</tr>';
}
$table_str=$table_str. "</tbody></table>";
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <meta name="robots" content="noindex, nofollow">
  <meta name="googlebot" content="noindex, nofollow">
  <!--<script type="text/javascript" src="//code.jquery.com/jquery-1.10.1.js"></script>-->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

  <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css">
  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
  
  
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.27.8/js/jquery.tablesorter.min.js"></script> 
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.27.8/css/theme.blue.min.css">
  <link rel="stylesheet" type="text/css" href="https://rawgit.com/flaviusmatis/simplePagination.js/master/simplePagination.css">
        <script type="text/javascript" src="https://rawgit.com/flaviusmatis/simplePagination.js/master/jquery.simplePagination.js"></script>
<link rel="stylesheet" type="text/css" href="theme.bootstrap.css">
  <script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.27.8/js/jquery.tablesorter.widgets.js"></script>

<!-- pager plugin -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.27.8/css/jquery.tablesorter.pager.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.27.8/js/extras/jquery.tablesorter.pager.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.27.8/js/widgets/widget-filter-formatter-jui.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.27.8/css/filter.formatter.min.css">
  <title>PriceTracker Grid</title>

  <script type='text/javascript'>//<![CDATA[
$(window).load(function(){
$(function() {
  $.tablesorter.themes.bootstrap = {
    // these classes are added to the table. To see other table classes available,
    // look here: http://getbootstrap.com/css/#tables
    table        : 'table table-bordered table-striped',
    caption      : 'caption',
    // header class names
    header       : 'bootstrap-header', // give the header a gradient background (theme.bootstrap_2.css)
    sortNone     : '',
    sortAsc      : '',
    sortDesc     : '',
    active       : '', // applied when column is sorted
    hover        : '', // custom css required - a defined bootstrap style may not override other classes
    // icon class names
    icons        : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
    iconSortNone : 'bootstrap-icon-unsorted', // class name added to icon when column is not sorted
    iconSortAsc  : 'glyphicon glyphicon-chevron-up', // class name added to icon when column has ascending sort
    iconSortDesc : 'glyphicon glyphicon-chevron-down', // class name added to icon when column has descending sort
    filterRow    : '', // filter row class; use widgetOptions.filter_cssFilter for the input/select element
    footerRow    : '',
    footerCells  : '',
    even         : '', // even row zebra striping
    odd          : ''  // odd row zebra striping
  };
  var $table = $('table'),
  // define pager options
  pagerOptions = {
    // target the pager markup - see the HTML block below
    container: $(".pager"),
    // output string - default is '{page}/{totalPages}';
    // possible variables: {size}, {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
    // also {page:input} & {startRow:input} will add a modifiable input in place of the value
    output: '{startRow} - {endRow} / {filteredRows} ({totalRows})',
    // if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
    // table row set to a height to compensate; default is false
    fixedHeight: true,
    // remove rows from the table to speed up the sort of large tables.
    // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
    removeRows: false,
    // go to page selector - select dropdown that sets the current page
    cssGoto: '.gotoPage'
  };

  // Initialize tablesorter
  // ***********************
  $("table").tablesorter({
    // this will apply the bootstrap theme if "uitheme" widget is included
    // the widgetOptions.uitheme is no longer required to be set
    theme : "bootstrap",

    widthFixed: true,

    headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

    // widget code contained in the jquery.tablesorter.widgets.js file
    // use the zebra stripe widget if you plan on hiding any rows (filter widget)
    widgets : [ "uitheme", "filter", "zebra","uitheme" ],

    widgetOptions : {
      // using the default zebra striping class name, so it actually isn't included in the theme variable above
      // this is ONLY needed for bootstrap theming if you are using the filter widget, because rows are hidden
      zebra : ["even", "odd"],

      // reset filters button
      filter_reset : ".reset",

      // extra css class name (string or array) added to the filter element (input or select)
      filter_cssFilter: "form-control",
      
      filter_formatter : {

        // Total column (jQuery selector added v2.17.0)
        '.total' : function($cell, indx){
          return $.tablesorter.filterFormatter.uiRange( $cell, indx, {
            delayed: true,        // delay search (set by filter_searchDelay)
            valueToHeader: false, // add current slider value to the header cell

            // add any of the jQuery UI Slider options (for range selection) here (http://api.jqueryui.com/slider/)
            values: [1, 200],     // starting range
            min: 1,               // minimum value
            max: 200              // maximum value
          });
        }

      }
      // set the uitheme widget to use the bootstrap theme class names
      // this is no longer required, if theme is set
      // ,uitheme : "bootstrap"

    }
  })
  .tablesorterPager({

    // target the pager markup - see the HTML block below
    container: $(".ts-pager"),

    // target the pager page select dropdown - choose a page
    cssGoto  : ".pagenum",

    // remove rows from the table to speed up the sort of large tables.
    // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
    removeRows: false,

    // output string - default is '{page}/{totalPages}';
    // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
    output: '{startRow} - {endRow} / {filteredRows} ({totalRows})'

  });
});//]]> 
});
</script>



  
</head>

<body>
  <?php include "header.php";?>

  <div class="container searchable products-grid">

    <div class="row">

<?php echo $table_str; ?>
</div>
</div>
<br>
<br>
<br>
<div align="center">
<a class="btn btn-info" role="button" id="prev_link" href="">Previous Page</a>
    
<a class="btn btn-info" role="button" id="next_link" href="">Next Page</a>
</div>
<div align="center">

    <?php echo $pagination_str ?>
</div>
<?php include 'footer.html';?>
<script type='text/javascript'>

function removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}

function search() {
    if(document.getElementById("search_box").value.length>0)
    {var link = removeParam('page',removeParam('s',window.location.href.toString()));
    if (link.indexOf('?') > -1) {
        var link =link+ '&s='+document.getElementById("search_box").value;
    } else {
        var link =link+ '?s='+document.getElementById("search_box").value;
    }
    window.location.href = link;
    }
    //alert(link);
}
</script>

<script type='text/javascript'>

function sortLinks(){
var elements = document.getElementsByClassName("sort_item");
for(var i=0; i<elements.length; i++) {
    var link = removeParam('page',removeParam('sort',window.location.href.toString()));
    
    if (link.indexOf('?') > -1) {
        var link =link+ '&';
        }
    else {
        var link =link+ '?';
    }
        var link =link+ elements[i].getAttribute('href');
    elements[i].setAttribute('href', link);
}
}

function nextLink(){
        var next_link=removeParam('page',window.location.href.toString());
        
        if (next_link.indexOf('?') > -1) {
            var prev_link=next_link+ '&page='+"<?php echo $page-1; ?>".toString();
            var next_link =next_link+ '&page='+"<?php echo $page+1; ?>".toString();
        
        } else {
            var prev_link=next_link+ '?page='+"<?php echo $page-1; ?>".toString();
            var next_link =next_link+ '?page='+"<?php echo $page+1; ?>".toString();
        }
        if(parseInt(<?php echo $page-1; ?>) <0 ) {
            $('#prev_link').attr('disabled', true);
            }
        if(<?php echo ($num_results/$RESULTS_PER_PAGE)."<=".($page+1); ?>) {
            $('#next_link').attr('disabled', true);
            }
        // FIXME add case for last page will need SqL COUnt(*)   
    document.getElementById("next_link").href=next_link;
        document.getElementById("prev_link").href=prev_link;

}
window.onload=function(){
    nextLink();
    sortLinks();
    $('[data-toggle="tooltip"]').tooltip(); 

} 
</script>