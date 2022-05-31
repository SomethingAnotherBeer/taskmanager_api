<?php
declare(strict_types = 1);
namespace App\Core;
use FastRoute;
use App\Http\Request;
use DI\Container;
use App\Helpers;
use App\Exceptions\HttpExceptions\NotFoundException;
use App\Exceptions\HttpExceptions\MethodNotAllowedException;

class Router
{
	private Container $container;
	private Request $request;


	public function __construct(Request $request,Container $container)
	{
		$this->request = $request;
		$this->container = $container;
	}

	public function route()
	{
		
		$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r){
				
			$r->addRoute('POST','/authentication/login','Authentication@login');
			$r->addRoute('POST','/authentication/logout','Authentication@logout');	
		
			$r->addRoute('POST','/user/createuser','User@createUser');
			$r->addRoute('PATCH','/user/changeuserlogin','User@changeUserLogin');
			$r->addRoute('PATCH', '/user/changeuserpassword','User@changeUserPassword');
			$r->addRoute('PATCH', '/user/changeuserfullname','User@changeUserFullName');
			$r->addRoute('DELETE','/user/deleteuser','User@deleteUser');
			
			$r->addRoute('GET','/users/getusers','Users@getUsers');
			$r->addRoute('GET','/users/getusersbystatus/{status_name}','Users@getUsersByStatus');
			$r->addRoute('GET','/users/getusersbygroup/{group_name}','Users@getUsersByGroup');
			$r->addRoute('GET','/users/getinrequestsonassociate','Users@getInRequestsOnAssociate');
			$r->addRoute('GET','/users/getoutrequestsonassociate','Users@getOutRequestsOnAssociate');
			$r->addRoute('GET','/users/getassociatedusers','Users@getAssociatedUsers');

			$r->addRoute('GET','/groups/getallgroups','Groups@getAllGroups');
			$r->addRoute('GET','/groups/getmygroups','Groups@getMyGroups');
			$r->addRoute('GET','/groups/getusergroups/{user_login}','Groups@getUserGroups');
			
			$r->addRoute('GET','/task/getformulatedtask/{task_key}','FormulatedTask@getFormulatedTask');
			$r->addRoute('POST','/task/formulatetaskforassociate','FormulatedTask@formulateTaskForAssociate');
			$r->addRoute('POST','/task/formulatetaskbygroup','FormulatedTask@formulateTaskByGroup');
			$r->addRoute('DELETE', '/task/dropformulatedtask','FormulatedTask@dropFormulatedTask');
			
			$r->addRoute('GET','/task/getentrustedtask/{task_key}', 'EntrustedTask@getEntrustedTask');
			$r->addRoute('POST','/task/inittask','EntrustedTask@initTask');
			$r->addRoute('POST','/task/starttask','EntrustedTask@startTask');
			$r->addRoute('POST','/task/stoptask','EntrustedTask@stopTask');
			$r->addRoute('POST','/task/completetask','EntrustedTask@completeTask');
			$r->addRoute('DELETE','/task/droptask','EntrustedTask@dropTask');
		
			$r->addRoute('GET','/tasks/getformulatedtasks','Tasks@getFormulatedTasks');
			$r->addRoute('GET','/tasks/getentrustedtasks','Tasks@getEntrustedTasks');

			$r->addRoute('POST','/group/creategroup','Group@createGroup');
			$r->addRoute('PATCH','/group/renamegroup','Group@renameGroup');
			$r->addRoute('DELETE','/group/deletegroup','Group@deleteGroup');

			$r->addRoute('POST','/userpergroup/inviteusertogroup','UserPerGroup@inviteUserToGroup');
			$r->addRoute('POST','/userpergroup/acceptinvitetogroup','UserPerGroup@acceptInviteToGroup');
			$r->addRoute('POST','/userpergroup/denyinvitetogroup','UserPerGroup@denyInviteToGroup');
			$r->addRoute('DELETE','/userpergroup/exitfromgroup','UserPerGroup@exitFromGroup');
			
			$r->addRoute('POST','/usersassociate/send', 'UsersAssociate@sendRequestOnAssociate');
			$r->addRoute('POST','/usersassociate/accept','UsersAssociate@acceptRequestOnAssociate');
			$r->addRoute('POST','/usersassociate/deny','UsersAssociate@denyRequestOnAssociate');
			$r->addRoute('DELETE','/usersassociate/remove','UsersAssociate@removeAssociate');

		});

		$request_method = $this->request->getRequestMethod();
		$uri = $this->request->getRequestUri();


		if(false !== $pos = strpos($uri, '?'))
		{
			$uri = substr($uri, 0, $pos);
		}
		$uri = rawurldecode($uri);

		$route_info = $dispatcher->dispatch($request_method,$uri);

		try
		{
			switch($route_info[0])
			{
				case FastRoute\Dispatcher::NOT_FOUND:
					throw new NotFoundException("Ресурс не найден");
				break;

				case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
					throw new MethodNotAllowedException("Данный метод не поддерживается на запрашиваемом ресурсе");
				break;

				case FastRoute\Dispatcher::FOUND:
					$handler = $route_info[1];
					$vars = $route_info[2];

					if(false !== strpos($handler,'@'))
					{
						$controller_params = explode('@', $handler);
						$controller_name = $controller_params[0]."Controller";
						$method_name = $controller_params[1];

						$controller = $this->container->get($controller_name);
						Helpers\clearGlobals();
						return (!empty($vars)) ? $controller->$method_name(...array_values($vars)) : $controller->$method_name();
					}
					
			}
		}
		catch(\Exception $e)
		{
			throw $e;
		}
	}




}