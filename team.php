<?php
/**
 * team.php
 *
 * @author Nicolas CARPi <nicolas.carpi@curie.fr>
 * @copyright 2012 Nicolas CARPi
 * @see http://www.elabftw.net Official website
 * @license AGPL-3.0
 * @package elabftw
 */
namespace Elabftw\Elabftw;

use PDO;

/**
 * The team page
 *
 */
require_once 'inc/common.php';
$page_title = _('Team');
$selected_menu = 'Team';
require_once 'inc/head.php';

$Users = new Users();
$TeamsView = new TeamsView();
$Database = new Database($_SESSION['team_id']);
$Scheduler = new Scheduler($_SESSION['team_id']);
?>

<script src='js/moment/moment.js'></script>
<script src='js/fullcalendar/dist/fullcalendar.js'></script>
<link rel='stylesheet' media='all' href='js/fullcalendar/dist/fullcalendar.min.css'>

<menu>
<ul>
<li class='tabhandle' id='tab0'><?= _('Booking') ?></li>
<li class='tabhandle' id='tab1'><?= _('Members') ?></li>
<li class='tabhandle' id='tab2'><?= _('Statistics')?></li>
<li class='tabhandle' id='tab3'><?= _('Tools') ?></li>
<li class='tabhandle' id='tab4'><?= _('Help') ?></li>
</ul>
</menu>

<!-- TAB 0 SCHEDULER -->
<div class='divhandle' id='tab0div'>
<select id='scheduler-select' onChange="insertParamAndReload('item', this.value)">
<option selected disabled><?= _("Select an equipment") ?></option>
<?php
$items = $Database->readAll();
foreach ($items as $item) {
    echo "<option ";
    if ($_GET['item'] == $item['id']) {
        echo "selected ";
    }
    echo "value='" . $item['id'] . "'>" . $item['title'] . "</option>";
}
?>
</select>
<?php
if (isset($_GET['item'])) {
    $Scheduler->setId($_GET['item']);
    echo "<div id='scheduler'></div>";
}
?>
</div>

<!-- TAB 1 MEMBERS -->
<div class='divhandle' id='tab1div'>
<?php display_message('ok_nocross', sprintf(_('You belong to the %s team.'), get_team_config('team_name'))) ?>

<table id='teamtable' class='table'>
    <tr>
        <th><?= _('Name') ?></th>
        <th><?= _('Phone') ?></th>
        <th><?= _('Mobile') ?></th>
        <th><?= _('Website') ?></th>
        <th><?= _('Skype') ?></th>
    </tr>
<?php
foreach ($Users->readAll() as $user) {
    echo "<tr>";
    echo "<td><a href='mailto:" . $user['email'] . "'><span";
    // put sysadmin, admin and chiefs in bold
    if ($user['usergroup'] === '3' || $user['usergroup'] === '1' || $user['usergroup'] === '2') {
        echo " style='font-weight:bold'";
    }
    echo ">" . $user['firstname'] . " " . $user['lastname'] . "</span></a>";
    echo "</td>";
    if (!empty($user['phone'])) {
        echo "<td>" . $user['phone'] . "</td>";
    } else {
        echo "<td>&nbsp;</td>"; // Placeholder
    }
    if (!empty($user['cellphone'])) {
        echo "<td>" . $user['cellphone'] . "</td>";
    } else {
        echo "<td>&nbsp;</td>";
    }
    if (!empty($user['website'])) {
        echo "<td><a href='" . $user['website'] . "'>" . $user['website'] . "</a></td>";
    } else {
        echo "<td>&nbsp;</td>";
    }
    if (!empty($user['skype'])) {
        echo "<td>" . $user['skype'] . "</td>";
    } else {
        echo "<td>&nbsp;</td>";
    }
}
?>
</table>
</div>

<!-- TAB 2 STATISTICS -->
<div class='divhandle' id='tab2div'>
    <p><?= $TeamsView->showStats($_SESSION['team_id']) ?></p>
</div>

<!-- TAB 3 TOOLS -->
<div class='divhandle chemdoodle' id='tab3div'>
    <h3><?php echo _('Molecule drawer'); ?></h3>
    <div class='box'>
        <link rel="stylesheet" href="css/chemdoodle.css" type="text/css">
        <script src="js/chemdoodle.js"></script>
        <script src="js/chemdoodle-uis.js"></script>
        <div class='center'>
            <script>
                var sketcher = new ChemDoodle.SketcherCanvas('sketcher', 550, 300, {oneMolecule:true});
            </script>
        </div>
    </div>
</div>

<!-- TAB 4 HELP -->
<div class='divhandle' id='tab4div'>
    <p>
        <ul>
        <li class='tip'><?= sprintf(_('There is a manual available %shere%s.'), "<a href='doc/_build/html/manual.html'>", "</a>") ?></li>
        <li class='tip'><?= _("You can use a TODOlist by pressing 't'.") ?></li>
        <li class='tip'><?= sprintf(_('You can have experiments templates (%sControl Panel%s).'), "<a href='ucp.php?tab=3'>", "</a>") ?></li>
        <li class='tip'><?= sprintf(_('The admin of a team can edit the status and the types of items available (%sAdmin Panel%s).'), "<a href='admin.php?tab=4'>", "</a>") ?></li>
        <li class='tip'><?= _('If you press Ctrl Shift D in the editor, the date will appear under the cursor.') ?></li>
        <li class='tip'><?= sprintf(_('Custom shortcuts are available (%sControl Panel%s).'), "<a href='ucp.php?tab=1'>", "</a>") ?></li>
        <li class='tip'><?= _('You can duplicate experiments in one click.') ?></li>
        <li class='tip'><?= _('Click a tag to list all items with this tag.') ?></li>
        <li class='tip'><?= _('Only a locked experiment can be timestamped.') ?></li>
        <li class='tip'><?= _('Once timestamped an experiment cannot be unlocked or modified. Only comments can be added.') ?></li>
        </ul>
    </p>
</div>
<!-- *********************** -->

<script>
$(document).ready(function() {
    // TABS
    // get the tab=X parameter in the url
    var params = getGetParameters();
    var tab = parseInt(params['tab']);
    if (!isInt(tab)) {
        var tab = 0;
    }
    var initdiv = '#tab' + tab + 'div';
    var inittab = '#tab' + tab;
    // init
    $(".divhandle").hide();
    $(initdiv).show();
    $(inittab).addClass('selected');

    $(".tabhandle" ).click(function(event) {
        var tabhandle = '#' + event.target.id;
        var divhandle = '#' + event.target.id + 'div';
        $(".divhandle").hide();
        $(divhandle).show();
        $(".tabhandle").removeClass('selected');
        $(tabhandle).addClass('selected');
    });
    // END TABS
	$('#scheduler').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
            defaultView: 'agendaWeek',
			selectable: true,
			selectHelper: true,
			select: function(start, end) {
				var title = prompt('Event Title:');
				var eventData;
				if (title) {
					eventData = {
						title: title,
						start: start,
						end: end
					};
					$('#calendar').fullCalendar('renderEvent', eventData, true); // stick? = true
				}
				$('#calendar').fullCalendar('unselect');
			},
			editable: true,
			eventLimit: true, // allow "more" link when too many events
            events: <?= $Scheduler->read() ?>,
            dayClick: function(date, jsEvent, view) {
                schedulerCreate(date.format());
            }
		});
});

function schedulerCreate(date) {
    $.post('app/controllers/SchedulerController.php', {
        create: true,
        item: $('#scheduler-select').val(),
        date: date
    });
}
</script>

<?php require_once('inc/footer.php');
