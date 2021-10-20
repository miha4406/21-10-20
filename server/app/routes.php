<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->get('/server', function (Request $request, Response $response) {
        $link = mysqli_connect('localhost','root','','test');
        if(!$link){ die("Can't connect!");}

        $result = mysqli_query($link, "SELECT username, msg FROM chat");
        $data = mysqli_fetch_all($result);


        mysqli_close($link);
        $response->getBody()->write(json_encode($data));       
        return $response;
    });

    $app->post('/server', function (Request $request, Response $response) {
        $link = mysqli_connect('localhost','root','','test');
        if(!$link){ die("Can't connect!");}

        $params = $request->getQueryParams();
        $name = $params['name']; $text = $params['text']; 
       
        $stmt = mysqli_prepare($link, "INSERT INTO chat(username,msg) VALUES(?,?)");
        mysqli_stmt_bind_param($stmt, "ss", $name, $text);
        mysqli_stmt_execute($stmt);
       

        mysqli_close($link);
        $response->getBody()->write('SENT OK');       
        return $response;
    });












    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
};
