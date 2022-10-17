<div class="panel-heading" style="font-size: 12px;">
    <?php
    if($management == 1):
    ?>
    <a href="/coaching-session" style="color: white;">New Linking</a>  |
    <a href="/linkee-pending" style="color: white;">Pending</a>  |
    <?php
    else:
    ?>
    <a href="/coaching-session" style="color: white;">Pending</a> | 
    <?php
    endif;
    ?>
    <a href="/gtky-list" style="color: white;">GTKY</a> |
    <a href="/gs-list" style="color: white;">GS</a> |
    <a href="/sb-list" style="color: white;">SB</a> |
    <a href="/sda-list" style="color: white;">SDA</a> |
    <a href="/view-ql" style="color: white;">QL</a> | 
    <a href="/list-ce" style="color: white;">CE</a> | 
    <a href="/acc-list" style="color: white;">AS</a>
    <?php
    if($management == 1):
    ?>
    | <a href="/own-linking" style="color: white;">Personal</a>
    <?php
    endif;
    ?>
</div>