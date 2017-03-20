<?php require_once 'header.php'?>
<nav class="navbar navbar-default navbar-static-top">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">Todo List:</a>
        </div>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div ng-app="App">
                <client-ui></client-ui>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php'?>