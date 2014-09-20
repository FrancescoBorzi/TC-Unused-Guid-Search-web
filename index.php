<?php
require_once("config.php");

$table_creature = 1;
$table_gameobject = 2;
$table_waypoint_scripts = 3;
$table_pool_template = 4;
$table_game_event = 5;
$table_creature_equip_template = 6;
$table_trinity_string = 7;

$table_creature_sel = $table_gameobject_sel = $table_waypoint_scripts_sel = $table_pool_template_sel = $table_game_event_sel = $table_creature_equip_template_sel = $table_trinity_string_sel = "";

if (isset($_GET['table']) && $_GET['table'] != "")
{
  switch ($_GET['table'])
  {
    case $table_creature:
      $table = "creature";
      $param = "guid";
      $table_creature_sel = "selected";
      break;

    case $table_gameobject:
      $table = "gameobject";
      $param = "guid";
      $table_gameobject_sel = "selected";
      break;

    case $table_waypoint_scripts:
      $table = "waypoint_scripts";
      $param = "guid";
      $table_waypoint_scripts_sel = "selected";
      break;

    case $table_pool_template:
      $table = "pool_template";
      $param = "entry";
      $table_pool_template_sel = "selected";
      break;

    case $table_game_event:
      $table = "game_event";
      $param = "eventEntry";
      $table_game_event_sel = "selected";
      break;

    case $table_creature_equip_template:
      $table = "creature_equip_template";
      $param = "entry";
      $table_creature_equip_template_sel = "selected";
      break;

    case $table_trinity_string:
      $table = "trinity_string";
      $param = "entry";
      $table_trinity_string_sel = "selected";
      break;
  }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Unused Guid Search">
    <meta name="author" content="ShinDarth">
    <title>TC Unused Guid Search</title>

    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <p class="h2 text-center"><img src="img/trinitycore.png" alt="TrinityCore">TrinityCore Unused Guid Search Tool</p>
      <hr>

      <form style="margin: auto;" class="form-inline" role="form" method="GET">
        <div class="form-group">
          <strong>Table:</strong>
          <select name="table" class="text-center">
            <option value="<?= $table_creature ?>"<?= $table_creature_sel ?>>`creature`</option>
            <option value="<?= $table_gameobject ?>"<?= $table_gameobject_sel ?>>`gameobject`</option>
            <option value="<?= $table_waypoint_scripts ?>"<?= $table_waypoint_scripts_sel ?>>`waypoint_scripts`</option>
            <option value="<?= $table_pool_template ?>"<?= $table_pool_template_sel ?>>`pool_template`</option>
            <option value="<?= $table_game_event ?>"<?= $table_game_event ?>>`game_event_sel`</option>
            <option value="<?= $table_creature_equip_template ?>"<?= $table_creature_equip_template_sel ?>>`creature_equip_template`</option>
            <option value="<?= $table_trinity_string ?>"<?= $table_trinity_string_sel ?>>`trinity_string`</option>
          </select>
        </div>
        <div class="form-group">
          <div class="input-group">
            <div class="input-group-addon">Starting from:</div>
            <input name="starting-from" style="max-width: 140px;" class="form-control" type="text" value="<?= $_GET['starting-from'] ?>" placeholder="1">
          </div>
        </div>
        <div class="form-group">
          <div class="input-group">
            <div class="input-group-addon">GUID amount:</div>
            <input name="amount" style="max-width: 140px;" class="form-control" type="text" value="<?= $_GET['amount'] ?>" placeholder="10">
          </div>
        </div>
        <button type="submit" class="btn btn-success">Search</button>
      </form>
      <br>
<?php

if (isset($_GET['table'])  && $_GET['table'] != null)
{
  if (isset($_GET['starting-from']) && $_GET['starting-from'] != null)
    $starting_from = $_GET['starting-from'];
  else
    $starting_from = 1;

  if (isset($_GET['amount']) && $_GET['amount'] != null)
    $amount = $_GET['amount'];
  else
    $amount = 10;

  $query_max_min = sprintf("SELECT MAX(%s), MIN(%s) FROM %s", $param, $param, $table);
  $result_max_min = $db->query($query_max_min);

  if (!$result_max_min)
    die("Error querying: " . $query_max_min);

  $row_max_min = mysqli_fetch_row($result_max_min);

  $MAX_GUID = $row_max_min[0];
  $MIN_GUID = $row_max_min[1];

  printf("<p class=\"text-center\">Table <strong>`%s`</strong> has MAX(%s) = <strong>%d</strong> and MIN(%s)= <strong>%d</strong></p>", $table, $param, $MAX_GUID, $param, $MIN_GUID);

  $query = sprintf("SELECT %s FROM `%s` WHERE %s >= %d ORDER BY %s ASC", $param, $table, $param, $starting_from, $param);
  $result = $db->query($query);

  if (!$result)
    die("Error querying: " . $query);

  $row = mysqli_fetch_row($result);
  $last = $row[0];

  $count = 0;

  printf("<div><pre>%s</pre></div>", $query);

  echo "<div><pre>";

  while (($row = mysqli_fetch_row($result)) != null)
  {
    if ($count >= $amount)
      break;

    $current = $row[0];

    if ($current != $last + 1)
    {
      for ($i = $last + 1; $i < $current; $i++)
      {
        if ($count >= $amount)
          break;

        printf("%d<br>", $i);
        $count++;
      }

      printf("<br>");
    }

    $last = $current;
  }

  echo "</pre></div>";
}

?>
      <hr>
      <p class="h4 text-center">Coded by <a href="http://www.github.com/ShinDarth">ShinDarth</a>&nbsp;<iframe style="vertical-align: middle;" src="http://ghbtns.com/github-btn.html?user=ShinDarth&repo=TC-Unused-Guid-Search-web&type=watch&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="110" height="20"></iframe></p>
      <a href="https://github.com/ShinDarth/TC-Unused-Guid-Search-web"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/38ef81f8aca64bb9a64448d0d70f1308ef5341ab/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png"></a>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
