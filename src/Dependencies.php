<?php declare(strict_types=1);

use Auryn\Injector;
use Ramsey\Uuid\Uuid;
use WinYum\Role\Domain\Role;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\UuidInterface;
use WinYum\Framework\Rbac\User;
use WinYum\Framework\Dbal\DatabaseUrl;
use WinYum\Role\Domain\RoleRepository;
use WinYum\User\Domain\UserRepository;
use WinYum\Framework\Csrf\TokenStorage;
use WinYum\Role\Application\RolesQuery;
use WinYum\Admin\Application\UsersQuery;
use WinYum\Framework\Dbal\ConnectionFactory;
use WinYum\Role\Infrastructure\DbalRolesQuery;
use WinYum\Admin\Infrastructure\DbalUsersQuery;
use WinYum\User\Application\NicknameTakenQuery;
use WinYum\Framework\Rendering\TemplateRenderer;
use WinYum\Framework\Rendering\TemplateDirectory;
use WinYum\FrontPage\Application\SubmissionsQuery;
use WinYum\Role\Infrastructure\DbalRoleRepository;
use WinYum\User\Application\UserTableIsEmptyQuery;
use WinYum\User\Infrastructure\DbalUserRepository;
use WinYum\Framework\Rbac\SymfonySessionRoleFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use WinYum\Framework\Csrf\SymfonySessionTokenStorage;
use WinYum\Role\Presentation\RegisterRoleFormFactory;
use GenericApp\Submission\Domain\SubmissionRepository;
use WinYum\User\Infrastructure\DbalNicknameTakenQuery;
use WinYum\FrontPage\Infrastructure\DbalSubmissionsQuery;
use WinYum\User\Infrastructure\DbalUserTableIsEmptyQuery;
use WinYum\SubmissionRole\Domain\SubmissionRoleRepository;
use WinYum\Framework\Rbac\SymfonySessionCurrentUserFactory;
use WinYum\Framework\Rendering\TwigTemplateRendererFactory;
use WinYum\SubmissionRole\Application\SubmissionsRoleQuery;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use GenericApp\Submission\Infrastructure\DbalSubmissionRepository;
use WinYum\SubmissionRole\Infrastructure\DbalSubmissionsRoleQuery;
use WinYum\SubmissionRole\Infrastructure\DbalSubmissionRoleRepository;

$injector = new Injector();

$injector->delegate(
    TemplateRenderer::class,
    function () use ($injector): TemplateRenderer {
        $factory = $injector->make(TwigTemplateRendererFactory::class);
        return $factory->create();
    }
);

$injector->define(TemplateDirectory::class, [':rootDirectory' => ROOT_DIR]);

$injector->define(
    DatabaseUrl::class,
    [':url' => 'sqlite:///' . ROOT_DIR . '/storage/db.sqlite3']
);

$injector->delegate(Connection::class, function () use ($injector): Connection {
    $factory = $injector->make(ConnectionFactory::class);
    return $factory->create();
});
$injector->share(Connection::class);


$injector->alias(SubmissionsQuery::class, DbalSubmissionsQuery::class);
$injector->share(SubmissionsQuery::class);
$injector->alias(SubmissionRepository::class, DbalSubmissionRepository::class);

$injector->alias(SubmissionsRoleQuery::class, DbalSubmissionsRoleQuery::class);
$injector->share(SubmissionsRoleQuery::class);
$injector->alias(SubmissionRoleRepository::class, DbalSubmissionRoleRepository::class);

$injector->alias(UsersQuery::class, DbalUsersQuery::class);
$injector->share(UsersQuery::class);
$injector->alias(UserRepository::class, DbalUserRepository::class);

$injector->alias(TokenStorage::class, SymfonySessionTokenStorage::class);

$injector->alias(SessionInterface::class, Session::class);

$injector->alias(UuidInterface::class, Uuid::class);
$injector->share(UuidInterface::class);

$injector->alias(NicknameTakenQuery::class, DbalNicknameTakenQuery::class);
$injector->alias(UserTableIsEmptyQuery::class, DbalUserTableIsEmptyQuery::class);

$injector->delegate(User::class, function () use ($injector): User {
    $factory = $injector->make(SymfonySessionCurrentUserFactory::class);
    return $factory->create();
});



return $injector;