<?php

declare(strict_types = 1);

use App\Http\Controllers\Api\V1\{CategoryController, ProductController, UserController, WarehouseController};
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class);

it('invokes stub methods on api v1 category controller', function (): void {
    $c   = new CategoryController();
    $req = Request::create('/');

    $c->index();
    $c->store($req);
    $c->show('1');
    $c->update($req, '1');
    $c->destroy('1');

    expect(true)->toBeTrue();
});

it('invokes stub methods on api v1 product controller', function (): void {
    $c   = new ProductController();
    $req = Request::create('/');

    $c->index();
    $c->store($req);
    $c->show('1');
    $c->update($req, '1');
    $c->destroy('1');

    expect(true)->toBeTrue();
});

it('invokes stub methods on api v1 user controller', function (): void {
    $c   = new UserController();
    $req = Request::create('/');

    $c->index();
    $c->store($req);
    $c->show('1');
    $c->update($req, '1');
    $c->destroy('1');

    expect(true)->toBeTrue();
});

it('invokes stub methods on api v1 warehouse controller', function (): void {
    $c   = new WarehouseController();
    $req = Request::create('/');

    $c->index();
    $c->store($req);
    $c->show('1');
    $c->update($req, '1');
    $c->destroy('1');

    expect(true)->toBeTrue();
});
