<?php

use MediaWiki\Extension\WikibaseManifest\ConceptNamespaces;
use MediaWiki\Extension\WikibaseManifest\ConfigEquivEntitiesFactory;
use MediaWiki\Extension\WikibaseManifest\ConfigExternalServicesFactory;
use MediaWiki\Extension\WikibaseManifest\ConfigMaxLagFactory;
use MediaWiki\Extension\WikibaseManifest\EmptyArrayCleaner;
use MediaWiki\Extension\WikibaseManifest\LocalSourceEntityNamespacesFactory;
use MediaWiki\Extension\WikibaseManifest\ManifestGenerator;
use MediaWiki\Extension\WikibaseManifest\TitleFactoryMainPageUrl;
use MediaWiki\Extension\WikibaseManifest\WbManifest;
use MediaWiki\MediaWikiServices;
use Wikibase\Repo\WikibaseRepo;

return [
	WbManifest::WIKIBASE_MANIFEST_GENERATOR => function ( MediaWikiServices $services ) {
		$mainPageUrl =
			$services->getService( WbManifest::WIKIBASE_MANIFEST_TITLE_FACTORY_MAIN_PAGE_URL );

		$equivEntitiesFactory =
			$services->getService( WbManifest::WIKIBASE_MANIFEST_CONFIG_EQUIV_ENTITIES_FACTORY );

		$conceptNamespaces = $services->getService( WbManifest::WIKIBASE_MANIFEST_CONCEPT_NAMESPACES );

		$externalServicesFactory = $services->getService( WbManifest::WIKIBASE_MANIFEST_CONFIG_EXTERNAL_SERVICES_FACTORY );

		$entityNamespacesFactory = $services->getService( WbManifest::WIKIBASE_MANIFEST_LOCAL_SOURCE_ENTITY_NAMESPACES_FACTORY );

		$maxLagFactory = $services->getService( WbManifest::WIKIBASE_MANIFEST_CONFIG_MAX_LAG_FACTORY );

		return new ManifestGenerator(
			$services->getMainConfig(),
			$mainPageUrl,
			$equivEntitiesFactory,
			$conceptNamespaces,
			$externalServicesFactory,
			$entityNamespacesFactory,
			$maxLagFactory
		);
	},
	WbManifest::WIKIBASE_MANIFEST_CONFIG_EQUIV_ENTITIES_FACTORY => function ( MediaWikiServices $services ) {
		return new ConfigEquivEntitiesFactory(
			$services->getMainConfig(), WbManifest::ENTITY_MAPPING_CONFIG
		);
	},
	WbManifest::WIKIBASE_MANIFEST_CONFIG_EXTERNAL_SERVICES_FACTORY => function ( MediaWikiServices $services ) {
		return new ConfigExternalServicesFactory(
			$services->getMainConfig(), WbManifest::EXTERNAL_SERVICES_CONFIG
		);
	},
	WbManifest::WIKIBASE_MANIFEST_CONCEPT_NAMESPACES => function () {
		$repo = WikibaseRepo::getDefaultInstance();
		$rdfVocabulary = $repo->getRdfVocabulary();
		$localEntitySource = $repo->getLocalEntitySource();
		// TODO: Get Canonical Document URLS from a service not straight from remote
		return new ConceptNamespaces( $localEntitySource, $rdfVocabulary );
	},
	WbManifest::EMPTY_ARRAY_CLEANER => function () {
		return new EmptyArrayCleaner();
	},
	WbManifest::WIKIBASE_MANIFEST_LOCAL_SOURCE_ENTITY_NAMESPACES_FACTORY => function ( MediaWikiServices $services
	) {
		$repo = WikibaseRepo::getDefaultInstance();
		$localEntitySource = $repo->getLocalEntitySource();

		return new LocalSourceEntityNamespacesFactory(
			$localEntitySource, $services->getNamespaceInfo()
		);
	},
	WbManifest::WIKIBASE_MANIFEST_TITLE_FACTORY_MAIN_PAGE_URL => function ( MediaWikiServices $services ) {
		return new TitleFactoryMainPageUrl( $services->getTitleFactory() );
	},
	WbManifest::WIKIBASE_MANIFEST_CONFIG_MAX_LAG_FACTORY => function ( MediaWikiServices $services ) {
		return new ConfigMaxLagFactory(
			$services->getMainConfig(), WbManifest::MAX_LAG_CONFIG
		);
	}
];
