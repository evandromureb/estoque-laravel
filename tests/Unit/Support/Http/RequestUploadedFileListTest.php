<?php

declare(strict_types = 1);

use App\Support\Http\RequestUploadedFileList;
use Illuminate\Http\{Request, UploadedFile};
use Tests\TestCase;

uses(TestCase::class);

it('returns empty list when the key has no file', function (): void {
    $request = Request::create('/', 'POST');

    expect(RequestUploadedFileList::asList($request, 'images'))->toBe([]);
});

it('wraps a single uploaded file in a list', function (): void {
    $file    = UploadedFile::fake()->image('one.jpg');
    $request = Request::create('/', 'POST', [], [], ['images' => $file]);

    $list = RequestUploadedFileList::asList($request, 'images');

    expect($list)->toHaveCount(1)
        ->and($list[0])->toBeInstanceOf(UploadedFile::class);
});

it('normalizes multiple files to a zero-indexed list', function (): void {
    $request = Request::create('/', 'POST', [], [], [
        'images' => [
            UploadedFile::fake()->image('a.jpg'),
            UploadedFile::fake()->image('b.jpg'),
        ],
    ]);

    $list = RequestUploadedFileList::asList($request, 'images');

    expect($list)->toHaveCount(2)
        ->and(array_keys($list))->toBe([0, 1]);
});

it('returns empty list when file payload is neither uploaded file nor array', function (): void {
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('hasFile')->with('doc')->andReturnTrue();
    $request->shouldReceive('file')->with('doc')->andReturn(new \stdClass());

    expect(RequestUploadedFileList::asList($request, 'doc'))->toBe([]);
});
