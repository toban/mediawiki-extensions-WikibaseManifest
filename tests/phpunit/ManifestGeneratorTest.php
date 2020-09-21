<?php

namespace WikibaseManifest\Test;

use HashConfig;
use MediaWiki\Extension\WikibaseManifest\ConceptNamespaces;
use MediaWiki\Extension\WikibaseManifest\EntityNamespaces;
use MediaWiki\Extension\WikibaseManifest\EntityNamespacesFactory;
use MediaWiki\Extension\WikibaseManifest\EquivEntities;
use MediaWiki\Extension\WikibaseManifest\EquivEntitiesFactory;
use MediaWiki\Extension\WikibaseManifest\ExternalServices;
use MediaWiki\Extension\WikibaseManifest\ExternalServicesFactory;
use MediaWiki\Extension\WikibaseManifest\ManifestGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \MediaWiki\Extension\WikibaseManifest\ManifestGenerator
 */
class ManifestGeneratorTest extends TestCase {

	public function testGenerate() {
		$siteString = 'manifestsite';
		$serverString = 'http://cat/dog';
		$apiAction = '/api.php';
		$apiRest = '/rest.php';
		$scriptString = '/wikipath';
		$equivEntities = [ 'P1' => 'P2' ];
		$mockConfig = new HashConfig(
			[
			'Server' => $serverString,
			'Sitename' => $siteString,
			'ScriptPath' => $scriptString,
			]
		);

		$mockEquivEntitiesFactory = $this->createMock( EquivEntitiesFactory::class );
		$mockEquivEntitiesFactory->expects( $this->once() )
			->method( 'getEquivEntities' )
			->willReturn( new EquivEntities( $equivEntities ) );

		$mockConceptNamespaces = $this->createMock( ConceptNamespaces::class );
		$mockConceptNamespaces->expects( $this->once() )
			->method( 'getLocal' )
			->willReturn( [ 'a' => 'bb' ] );

		$mockExternalServicesFactory = $this->createMock( ExternalServicesFactory::class );
		$externalServicesMappings = [ 'queryservice' => 'http://services.something' ];
		$mockExternalServicesFactory->expects( $this->once() )
			->method( 'getExternalServices' )
			->willReturn( new ExternalServices( $externalServicesMappings ) );

		$entityNamespaceMapping = [ 'item' => [ 'namespace_id' => 123, 'namespace_string' => 'Cat' ] ];
		$mockEntityNamespacesFactory = $this->createMock( EntityNamespacesFactory::class );
		$mockEntityNamespacesFactory->expects( $this->atLeastOnce() )
			->method( 'getEntityNamespaces' )
			->willReturn( new EntityNamespaces( $entityNamespaceMapping ) );
		$generator = new ManifestGenerator(
			$mockConfig,
			$mockEquivEntitiesFactory,
			$mockConceptNamespaces,
			$mockExternalServicesFactory,
			$mockEntityNamespacesFactory
		);
		$result = $generator->generate();

		$expectedSubset = [
			'name' => $siteString,
			'root_script_url' => $serverString . $scriptString,
			'api' => [
				'action' => $serverString . $scriptString . $apiAction,
				'rest' => $serverString . $scriptString . $apiRest
			],
			'equiv_entities' => [
				'wikidata.org' => $equivEntities,
			],
			'local_rdf_namespaces' => [ 'a' => 'bb' ],
			'external_services' => $externalServicesMappings,
			'entity_sources' => $entityNamespaceMapping
		];

		foreach ( $expectedSubset as $key => $value ) {
			$this->assertArrayHasKey( $key, $result );
			$this->assertSame( $value, $result[$key] );
		}
	}
}
