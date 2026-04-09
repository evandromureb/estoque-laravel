<?php

declare(strict_types = 1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\{BoolReturnTypeFromBooleanStrictReturnsRector, NumericReturnTypeFromStrictReturnsRector, NumericReturnTypeFromStrictScalarReturnsRector, ReturnTypeFromStrictConstantReturnRector, ReturnTypeFromStrictFluentReturnRector, ReturnTypeFromStrictNativeCallRector, ReturnTypeFromStrictNewArrayRector, ReturnTypeFromStrictTernaryRector, ReturnTypeFromStrictTypedCallRector, ReturnTypeFromStrictTypedPropertyRector};
use RectorLaravel\Rector\MethodCall\{AddArgumentDefaultValueRector, AddGenericReturnTypeToRelationsRector, AddParentBootToModelClassMethodRector, AddParentRegisterToEventServiceProviderRector, AssertStatusToAssertMethodRector, AvoidNegatedCollectionFilterOrRejectRector, ChangeQueryWhereDateValueWithCarbonRector, ContainerBindConcreteWithClosureOnlyRector, ConvertEnumerableToArrayToAllRector, DatabaseExpressionToStringToMethodCallRector, EloquentOrderByToLatestOrOldestRector, EnvVariableToEnvHelperRector, FactoryApplyingStatesRector, MakeModelAttributesAndScopesProtectedRector, MigrateToSimplifiedAttributeRector, ModelCastsPropertyToCastsMethodRector, PropertyDeferToDeferrableProviderToRector, RefactorBlueprintGeometryColumnsRector, ReplaceExpectsMethodsInTestsRector, ReplaceServiceContainerCallArgRector, RequestVariablesToRequestFacadeRector, ReverseConditionableMethodCallRector, ScopeNamedClassMethodToScopeAttributedClassMethodRector, ServerVariableToRequestFacadeRector, SessionVariableToSessionFacadeRector, UnaliasCollectionMethodsRector, UseComponentPropertyWithinCommandsRector};

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/resources/views',
    ]);

    RectorConfig::configure()
        ->withRules([
            UnaliasCollectionMethodsRector::class,
            ConvertEnumerableToArrayToAllRector::class,
            AvoidNegatedCollectionFilterOrRejectRector::class,
            ReverseConditionableMethodCallRector::class,
            EloquentOrderByToLatestOrOldestRector::class,
            ModelCastsPropertyToCastsMethodRector::class,
            AddParentBootToModelClassMethodRector::class,
            ScopeNamedClassMethodToScopeAttributedClassMethodRector::class,
            AddGenericReturnTypeToRelationsRector::class,
            MigrateToSimplifiedAttributeRector::class,
            MakeModelAttributesAndScopesProtectedRector::class,
            RefactorBlueprintGeometryColumnsRector::class,
            ChangeQueryWhereDateValueWithCarbonRector::class,
            DatabaseExpressionToStringToMethodCallRector::class,
            SessionVariableToSessionFacadeRector::class,
            ServerVariableToRequestFacadeRector::class,
            EnvVariableToEnvHelperRector::class,
            RequestVariablesToRequestFacadeRector::class,
            ReplaceServiceContainerCallArgRector::class,
            UseComponentPropertyWithinCommandsRector::class,
            FactoryApplyingStatesRector::class,
            PropertyDeferToDeferrableProviderToRector::class,
            AddParentRegisterToEventServiceProviderRector::class,
            ContainerBindConcreteWithClosureOnlyRector::class,
            AssertStatusToAssertMethodRector::class,
            ReplaceExpectsMethodsInTestsRector::class,
            AddArgumentDefaultValueRector::class,
            ReturnTypeFromStrictTernaryRector::class,
            ReturnTypeFromStrictNewArrayRector::class,
            ReturnTypeFromStrictNativeCallRector::class,
            NumericReturnTypeFromStrictReturnsRector::class,
            NumericReturnTypeFromStrictScalarReturnsRector::class,
            BoolReturnTypeFromBooleanStrictReturnsRector::class,
            ReturnTypeFromStrictTypedPropertyRector::class,
            ReturnTypeFromStrictFluentReturnRector::class,
            ReturnTypeFromStrictConstantReturnRector::class,
            ReturnTypeFromStrictTypedCallRector::class,
        ])
        ->withPreparedSets(
            deadCode: true,
            codeQuality: true,
            naming: true
        );

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
    ]);
};
