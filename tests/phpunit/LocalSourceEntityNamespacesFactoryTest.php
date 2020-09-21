<?php

namespace MediaWiki\Extension\WikibaseManifest;

use NamespaceInfo;
use PHPUnit\Framework\TestCase;
use Wikibase\DataAccess\EntitySource;

class LocalSourceEntityNamespacesFactoryTest extends TestCase {

	public function testGetEntityNamespaces() {
		$itemNamespaceId = 0;
		$propertyNamespaceId = 123;
		$itemNamespaceString = '';
		$propertyNamespaceString = 'Property';
		$entityNamespaceIds = [
			'item' => $itemNamespaceId,
			'property' => $propertyNamespaceId,
		];

		$localEntitySource = $this->createMock( EntitySource::class );
		$localEntitySource->expects( $this->once() )->method( 'getEntityNamespaceIds' )->willReturn(
				$entityNamespaceIds
			);

		$namespaceInfo = $this->createMock( NamespaceInfo::class );
		$namespaceInfo->expects( $this->any() )->method( 'getCanonicalName' )->willReturnMap(
				[
					[ $itemNamespaceId, $itemNamespaceString ],
					[ $propertyNamespaceId, $propertyNamespaceString ],
				]
			);

		$entityNamespacesFactory = new LocalSourceEntityNamespacesFactory(
			$localEntitySource, $namespaceInfo
		);

		$this->assertEquals(
			[
				'item' => [
					'namespace_id' => $itemNamespaceId,
					'namespace_string' => $itemNamespaceString,
				],
				'property' => [
					'namespace_id' => $propertyNamespaceId,
					'namespace_string' => $propertyNamespaceString,
				],
			],
			$entityNamespacesFactory->getEntityNamespaces()->toArray()
		);
	}
}
