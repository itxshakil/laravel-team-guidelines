<?php

declare(strict_types=1);

use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Config\RectorConfig;
use Rector\Naming\Rector\Assign\RenameVariableToMatchMethodCallReturnTypeRector;
use Rector\Naming\Rector\ClassMethod\RenameParamToMatchTypeRector;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/bootstrap',
        __DIR__ . '/config',
        __DIR__ . '/public',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])

    ->withImportNames(removeUnusedImports: true)

    ->withComposerBased(laravel: true)

    ->withPhpSets(php83: true)

    ->withPhpVersion(PhpVersion::PHP_83)

    ->withSets([
        LaravelLevelSetList::UP_TO_LARAVEL_110,

        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_FACADE_ALIASES_TO_FULL_NAMES,
        LaravelSetList::LARAVEL_COLLECTION,
        LaravelSetList::LARAVEL_ARRAYACCESS_TO_METHOD_CALL,
        LaravelSetList::LARAVEL_TYPE_DECLARATIONS,
        LaravelSetList::LARAVEL_IF_HELPERS,

        SetList::TYPE_DECLARATION,
        SetList::TYPE_DECLARATION_DOCBLOCKS,

        SetList::CODE_QUALITY,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
        SetList::DEAD_CODE,
        SetList::RECTOR_PRESET,
        SetList::GMAGICK_TO_IMAGICK,
    ])

    ->withConfiguredRule(RemoveDumpDataDeadCodeRector::class, [
        'dd',
        'dump',
        'var_dump',
    ])

    ->withRules([
        Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector::class,
        Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector::class,
    ])

    ->withPreparedSets(
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        true,
    )->withSkip([
        RenameVariableToMatchMethodCallReturnTypeRector::class,
        RenameParamToMatchTypeRector::class,
        PostIncDecToPreIncDecRector::class,
    ]);
