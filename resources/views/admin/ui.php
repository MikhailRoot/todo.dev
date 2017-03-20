<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Todos Admin UI</title>
    <link rel="stylesheet" href="/css/styles.min.css">
    <script src="/js/adminApp.js"></script>
</head>
<body>
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
            <div ng-app="AdminApp">
                <admin-ui></admin-ui>
            </div>
        </div>
    </div>
</div>
</body>
</html>