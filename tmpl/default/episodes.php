<?php
/**
 * episode listing
 *
 * @url         $URL$
 * @date        $Date$
 * @version     $Revision$
 * @author      $Author$
 * @license     GPL
 *
/**/

// Set the desired page title
    $page_title = 'MythWeb - '.t('TV Episodes');

// Mythweb headers
    $headers[] = '<link rel="stylesheet" type="text/css"      href="'.skin_url.'/tv_upcoming.css">';

// Print the page header
    require 'modules/_shared/tmpl/'.tmpl.'/header.php';

// Load includes
    require_once 'includes/episode_utils.php';

    $remainingEpisodes = $totalEpisodes-$totalRecorded;
    $fixedTitle        = stripslashes($fixedTitle);
    $showTitle         = stripslashes($showTitle);

// Get the image size of the picture and load it into an array
    if (file_exists("$imageDir/$showId.jpg")) {
        $imageInfo = getimagesize("$imageDir/$showId.jpg"); 
    } else {
        if (!file_exists("$imageDir/noImage.jpg"))
            copy("$scriptDir/noImage.jpg", "$imageDir/noImage.jpg");
        $showId = "noImage";
        $imageInfo = getimagesize("$imageDir/noImage.jpg");
    }

?>

<!-- This stuff should eventually go into a .css file -->
<style type="text/css">
td.x-active {
    padding:            .35em .5em;
    border-left:        1px solid #304943;
    height:             2em;
    background-color:   #485;
}

div.showinfo a {
    display:         block;
    color:           white;
    text-decoration: none;
    padding:         .2em .8em;
}

div.showinfo a span {
    display:none;
}

div.showinfo a:hover span {
    display:          block;
    position:         absolute;
    left:             33%;
    right:            33%;
    background-color: white;
    color:            #204670;
    right:            1px;
    margin-top:       0px;
    width:            550px;
    padding:          5px;
    border:           thin dashed #88a;
}
</style>


<script> 
function my_select() { 
    frm = document.forms.test; 
    ele = frm["f[]"];
    len = ele.length;
    type = true;
    for (i = 0; i < len; i++) {
        if (ele[i].checked == true) {
            type = false;
            break;
        }
    }
    for (i = 0; i < len; i++) { 
        ele[i].checked = type;
    }
}

</script> 


<table width="100%" border="0" cellpadding="0" cellspacing="0" >
  <td width="20%" align="center">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" >
    <tr>
    <div class="showinfo">
      <a href=<?php echo "http:$showLink"?> target="_blank">
      <span align=left><?php echo "$showData"?></span><img src="data/episode/images/<?php echo $showId?>.jpg" <?php echo imageResize($imageInfo[0], $imageInfo[1], $config['thumbnailSize']); ?>></a>
    </div>
    </tr>
  </table>
  </td>

  <td width="60%">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" >
  <tr>
    <td>
    <?php require 'modules/episode/tmpl/'.tmpl.'/menu.php'; ?>
    <table width="100%" border="0" cellpadding="2" cellspacing="0" >
      <tr align="center">
        <td>
          <font size=5> 
            <?php if (!$showData) $showData = "Update Episode Listing" ?>
            <a href=<?php echo "http:$showLink"?> target="_blank"><?php echo "$longTitle"?></a>
          </font>
        </td>
      </tr>
      <tr align="center">
        <td>
        <form id="match_subtitle" action="<?php echo root_url ?>episode/episodes?allepisodes=all&subMatch=yes" method="post">
          <?php echo t('Disable Subtitle Matching') ?>: <input type="checkbox" name="subtitle_match" class="radio" onclick="ajax_add_request(); $('match_subtitle').submit()"<?php if ($subMatchDis) echo ' CHECKED'?>>
        </form>
          &nbsp;&nbsp; | &nbsp;&nbsp;
        <form class="form" action="episode/episodes?state=update" method="post">
          <select name="display_site">
          <?php
          foreach(array('TVRage.com', 'TheTVDB.com') as $value) {
              echo '<option value="'.$value.'" ';
              if ($value == $config['siteSelect'])
                  echo ' SELECTED ';
              if ($value == 'null')
                  $value = t('TVRage.com');
              echo '>'.$value.'</option>';
          }
          ?>
          </select>

          <input type="submit" onclick="ajax_add_request()" name="update" value="<?php echo t('Update') ?>">
        </form>
        </td>
      </tr>
    </table>
    </td>
  </tr>

  <tr>
    <td>
    <table id="display_options" class="commandbox commands" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="x-title"><?php echo t('Display') ?>:</td>
        <td class="<?php echo ($_SESSION['episodes']['allepisodes'] == "all")?("x-active"):("x-check") ?>">
          <a onclick="ajax_add_request()" href="episode/episodes/?allepisodes=all"> 
          <?php echo t('All Episodes') ?>:<?php echo " $totalEpisodes"?>
        </td>
        <td class="<?php echo ($_SESSION['episodes']['title'])?("x-active"):("x-check") ?>">
          <a onclick="ajax_add_request()" href="episode/episodes/?title=<?php echo $showTitle?>">
          <?php echo t('Recorded') ?>:<?php echo " $totalRecorded"?>
        </td>
        <td class="<?php echo ($_SESSION['episodes']['allepisodes'] == "none")?("x-active"):("x-check") ?>">
          <a onclick="ajax_add_request()" href="episode/episodes/?allepisodes=none">
          <?php echo t('Not Recorded') ?>:<?php echo " $remainingEpisodes"?>
        </td>
        <td class="<?php echo ($_SESSION['episodes']['allepisodes'] == "sched")?("x-active"):("x-check") ?>">
          <a onclick="ajax_add_request()" href="episode/episodes/?allepisodes=sched">
          <?php echo t('Scheduled') ?>:<?php echo " $totalSched"?>
        </td>
      </tr>
    </table>
    </td>
  </tr>
  </table>
  </td>
  <td width="20%" align="center">
  <table width="100%" border="0" cellpadding="0" cellspacing="0" >
    <tr><td align="left">Start Date:</td><td align="left"><?php echo "$showStart"?></td></tr>
    <tr><td align="left">End Date:</td><td align="left"><?php echo "$showEnd"?></td></tr>
    <tr><td align="left">Country:</td><td align="left"><?php echo "$showCtry"?></td></tr>
    <tr><td align="left">Status:</td><td align="left"><?php echo "$showStatus"?></td></tr>
    <tr><td align="left">Classification:</td><td align="left"><?php echo "$showClass"?></td></tr>
    <tr><td align="left">Genre:</td><td align="left"><?php echo "$showGenre"?></td></tr>
    <tr><td align="left">Network:</td><td align="left"><?php echo "$showNetwork"?><br></td></tr>
    <tr><td align="left"><br><strong>Last Update:</td><td align="left"><br><strong><?php echo "$fileTime"?></td></tr>
  </table>
  </td>
</table>

<?php
if (isset($_SESSION['episodes']['allepisodes'])) { 
?>

<form name="test" action="episode/tvwish_list" method="post">
    <table width="100%" border="0" cellpadding="4" cellspacing="2" class="list small">
      <tr class="menu" align="left">
        <?php 
        if(!$tvwishHide) {
        ?>
        <td>Select</td>
        <?php 
        }
        ?>
        <td>Episode Number</td>
        <td>Original Airdate</td>
        <td>Subtitle</td>
        <td>Synopsis</td>
        <td>Status</td>	
      </tr>

    <?php

    $close_matchPosition = -1;
    //The purpose of this function is to match shows that have a very
    //similar subtitle. Ex. Altar Ego - Alter Ego
        function close_match($key, $arrayvalue, $matchPercent) {
            global $close_matchPosition;
            $close_matchPosition=0;
            foreach ($arrayvalue as $match) {
                similar_text($match, $key, $p);
                if ($p >= $matchPercent) return TRUE;
                    $close_matchPosition = $close_matchPosition + 1;
           }
        }

        foreach ($showEpisodes as $episodeData) {
        // Parse the show episode information for display 
            if (preg_match('/^INFO/', $episodeData)) continue;
            $episodeData = rtrim($episodeData); // Remove trailing whitespace
            $data        = explode("\t", $episodeData); // Split on tabs
            $epNumber    = $data[0]; // Episode Number
            $epDate      = $data[1]; // Episode Date
            $epSubtitle  = $data[2]; // Episode Subtitle
            $epUrl       = $data[3]; // Episode URL
            $epSummary   = $data[4]; // Episode Summary
            $dat         = preg_replace('/\([1-9]\)/', '', $epSubtitle);
            $dat         = trim($dat); // Remove leading and trailing whitespace
            $markSub     = preg_replace('/\#/', '', $epSubtitle);
            $epSummary   = preg_replace('/<.+?>/', '', $epSummary);
            $shortSub    = strtolower($dat);
            $shortSub    = preg_replace('/[^0-9a-z ]+/i', '', $shortSub);
            $shortSub    = preg_replace('/[^\w\d\s]+�/i', '', $shortSub);
            $shortSub    = preg_replace('/(?: and | the | i | or | of |the | a | in )/i', '', $shortSub);
            $shortSub    = preg_replace('/\s+/', '', $shortSub);
            $shortSub    = preg_replace('/[\/\;]/', '', $shortSub);

            $classes = "";

        // Check for date matches first and then subtitle.  I do this since some
        // episodes have bogus subtitles or no subtitle. 
            if ($unwatchedMatch = in_array("$epDate", $unwatchedDate) || 
               ((!$subMatchDis) && ($unwatchedMatch = close_match("$shortSub", $unwatchedEpisodes, $matchPercent)))) {
                if ($allEpisodes != "all") {
                    $boxCheck = "unchecked";
                    continue;
                }
                $classes .= " cat_Sports will_record";
                $boxCheck = "unchecked";
            }elseif ($watchedMatch = in_array("$epDate", $watchedDate) || 
                (!$subMatchDis) && (($watchedMatch = close_match("$shortSub", $watchedEpisodes, $matchPercent)))) {
                if ($allEpisodes != "all") {
                    $boxCheck = "unchecked";
                    continue;
                }
                $classes .= " deactivated";
                $boxCheck = "unchecked";
            }elseif ($videoMatch = in_array("$epNumber", $videoSE) || 
            // Check MythVideo files for matches first using Season/Episode, then
            // date matches and then subtitle
                ($videoMatch = in_array("$epDate", $videoDate) || 
                ($videoMatch = close_match("$shortSub", $videoEpisodes, $matchPercent)))) {
                if ($allEpisodes != "all") {
                    $boxCheck = "unchecked";
                    continue;
                }
                $classes .= " deactivated";
                $boxCheck = "unchecked";
            }elseif (($schedMatch = ($schedMatchDate = in_array("$epDate", $schedDate))) ||
                     ((!$subMatchDis) && ($schedMatch = close_match("$shortSub", $schedEpisodes, $matchPercent)))) {
                if($schedMatchDate) {
                    $schedEpisodesDetails[$schedEpisodes[array_search("$epDate", $schedDate)]]["matched"] = true;
                } else {
                    $schedEpisodesDetails["$shortSub"]["matched"] = true;
                }
                $classes .= " scheduled";
                $boxCheck = "unchecked";
            }elseif ($prevMatch = in_array("$epDate", $recDate) || 
                ((!$subMatchDis) && ($prevMatch = close_match("$shortSub", $recEpisodes, $matchPercent)))) {
                if ($allEpisodes != "all") {
                    $boxCheck = "unchecked";
                    continue;
                }
                $classes .= " deactivated";
                $boxCheck = "unchecked";
            } else {
                if ($_SESSION['episodes']['allepisodes'] == "sched") continue;
                $classes .= " duplicate";
                $boxCheck = "checked";
            }
        ?>

        <?php

            if (((preg_match('/^Season/', $epNumber)) || (preg_match('/^Special/', $epNumber))) && (!$special)) {
                $special = 1;
        ?>
            <tr class="menu" align="left">
              <td>Special Episodes</td>
            </tr> 
            <tr class="menu" align="left">
              <?php 
              if(!$tvwishHide) {
              ?>
              <td>Select</td>
              <?php 
              }
              ?>
              <td>Episode Number</td>
              <td>Original Airdate</td>
              <td>Subtitle</td>
              <td>Synopsis</td>
              <td>Status</td>
            </tr>
       <?php
       }

       ?>

            <tr class="<?php echo $classes ?>" align="left">
              <?php 
                  if(!$tvwishHide) {
              ?>
              <td class="<?php echo $classes ?>">
                <input type="checkbox" <?php echo $boxCheck?> name="f[]" value="<?php echo htmlspecialchars($epSubtitle)?>">
              </td>
              <?php 
                  }
              ?>
     
        <td class="<?php echo $classes ?>">
          <?php echo htmlspecialchars($epNumber)?>
        </td>

        <td class="<?php echo $classes ?>">
          <?php echo htmlspecialchars($epDate)?>
        </td>
 
        <?php
            if ($epUrl != "") {
        ?>
            <td class="<?php echo $classes ?>">
              <a href=<?php echo $epUrl?> target="_blank"><?php echo htmlspecialchars($epSubtitle)?></a>
            </td>

        <?php
            } else {
        ?>
            <td class="<?php echo $classes ?>">
              <?php echo htmlspecialchars($epSubtitle)?>
            </td>

        <?php
            }
        ?>

        <td width="60%" class="<?php echo $classes ?>">
          <?php echo $epSummary?>
        </td>

        <td class="<?php echo $classes?>">
          <?php
            if ($unwatchedMatch) {
                echo "Recorded - Unwatched!";
            } elseif ($watchedMatch) {
                echo "Recorded - Watched";
            } elseif ($videoMatch) {
                echo "Video File";
            } elseif ($schedMatch) {
                echo "Scheduled to Record";
            } elseif ($prevMatch) {
                echo "Previously Recorded";
            } else {
          ?> 
                <a onclick="ajax_add_request()" href="episode/episodes/?mark=yes&marktitle=<?php echo urlencode($fixedTitle)?>&marksubtitle=<?php echo urlencode($markSub)?>&markairdate=<?php echo htmlspecialchars($epDate)?>&marksummary=<?php echo htmlspecialchars($epSummary)?>">
                <?php echo t('Mark as Recorded') ?>
          <?php
            }
          ?>
          <?php /*if ($prevMatch) echo "Previously Recorded"?>
          <?php if ($schedMatch) echo "Scheduled to Record"?>
          <?php if (!$prevMatch && !$schedMatch) echo "Not Recorded"*/?>
        </td>
        </tr></tr>

    <?php
        } 
        $_SESSION['episodes']['allepisodes'] = "all";
        $classes = " record_duplicate scheduled";
        foreach ($schedEpisodesDetails as $logKey => $episodeArray) {
            if (!$episodeArray["matched"] && !in_array($logKey, $recEpisodes)) {
    ?>
    <tr class="<?php echo $classes ?>" align="left">
             <?php 
                 if(!$tvwishHide) {
             ?>
      <td class="<?php echo $classes ?>">
        &nbsp;
      </td>
             <?php 
                 }
             ?>
       
      <td class="<?php echo $classes ?>">
        <?php echo htmlspecialchars($episodeArray["syndicatedepisodenumber"])?>
      </td>

      <td class="<?php echo $classes ?>">
        <?php echo htmlspecialchars($episodeArray["airdate"])?>
      </td>
   
      <td class="<?php echo $classes ?>">
        <?php echo htmlspecialchars($episodeArray["subtitle"])?>
      </td>
  
      <td width="60%" class="<?php echo $classes ?>">
        <?php echo $episodeArray["description"]?>
      </td>
  
      <td class="<?php echo $classes?>">Unmatched but Scheduled to Record
      </td>
    </tr></tr>
  <?php
  	   }
      }
  ?>
  <?php
      if(!$tvwishHide) {
  ?>
    <tr class="menu">
      <td>
        <input type="button" value="Toggle" onClick="my_select(<?php echo "$toggleSelect" ?>);">
      </td>
      <td colspan="6" align="center">
        <input type="hidden" value="<?php echo "$fixedTitle"?>" name="title">
        <input type="submit" value="Create tvwish list" name="submit" id="submit">
      </td>
    </tr>
  <?php 
      }
  ?>
  </table>	
</form>

<?php
    }

if (isset($_SESSION['episodes']['title'])) {
?>

    <table width="100%" border="0" cellpadding="4" cellspacing="2" class="list small">
    <tr class="menu">
      <td><?php echo t('Title')?></a></td>
      <td><?php echo get_sort_link_recorded('subtitle',t('Subtitle'), 'title='. $showTitle)?></a></td>
      <td><?php echo t('Date Recorded')?></a></td>
      <td><?php echo get_sort_link_recorded('programid',t('Programid'), 'title='. $showTitle)?></a></td>
      <td><?php echo get_sort_link_recorded('description',t('Synopsis'), 'title='. $showTitle)?></a></td>
      <?php/*<td><?php echo t('Delete')?></td>*/?>
    </tr>

    <?php

        $row = 0;

        foreach ($All_Shows as $recdata) {
            list($startdate, $time) = explode(" ", $recdata->starttime);
    ?>
        <tr class="deactivated">
          <td><?php echo $recdata->title?></td>
          <td><?php echo $recdata->subtitle?></td>
          <td><?php echo $startdate?></td>
          <td><?php echo $recdata->programid?></td>
          <td><?php echo $recdata->description?></td>

          <td class="x-commands commands"><a onclick="ajax_add_request()" href="episode/episodes/?delete=yes&programid=<?php echo urlencode($recdata->programid)?>&title=<?php echo urlencode($recdata->title)?>" title="<?php echo t('Delete this episode') ?>"><?php echo t('Delete') ?></a></td>
 
        </tr>
    <?php
            $row++;
        }
    ?>

    </table>

<?php
    }

// Print the page footer
    require 'modules/_shared/tmpl/'.tmpl.'/footer.php';
?>
