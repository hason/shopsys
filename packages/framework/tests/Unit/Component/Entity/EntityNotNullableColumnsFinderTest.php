<?php

namespace Tests\FrameworkBundle\Unit\Component\Entity;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Entity\EntityNotNullableColumnsFinder;

class EntityNotNullableColumnsFinderTest extends TestCase
{
    public function testGetAllNotNullableColumnNamesIndexedByTableName()
    {
        $classMetadataInfoMock = $this->createMock(ClassMetadataInfo::class);
        $classMetadataInfoMock
            ->method('getTableName')
            ->willReturn('EntityName');
        $classMetadataInfoMock
            ->method('getFieldNames')
            ->willReturn(['notNullableField', 'nullableField']);
        $classMetadataInfoMock
            ->method('isNullable')
            ->willReturnCallback(function ($fieldName) {
                if ($fieldName === 'nullableField') {
                    return true;
                }
                return false;
            });
        $classMetadataInfoMock
            ->method('getColumnName')
            ->willReturnCallback(function ($fieldName) {
                if ($fieldName === 'notNullableField') {
                    return 'not_nullable_field';
                }
            });
        $associationMapping1['joinColumns'] = [
            [
                'nullable' => true,
                'name' => 'nullable_association',
            ],
        ];
        $associationMapping2['joinColumns'] = [
            [
                'nullable' => false,
                'name' => 'not_nullable_association',
            ],
        ];
        $associationMappings = [$associationMapping1, $associationMapping2];
        $classMetadataInfoMock
            ->method('getAssociationMappings')
            ->willReturn($associationMappings);

        $expectedResult = [
            'EntityName' => [
                'not_nullable_field',
                'not_nullable_association',
            ],
        ];

        $entityNotNullableColumnsFinder = new EntityNotNullableColumnsFinder();
        $actualResult = $entityNotNullableColumnsFinder->getAllNotNullableColumnNamesIndexedByTableName([$classMetadataInfoMock]);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetAllNotNullableColumnNamesIndexedByTableNameException()
    {
        $classMetadataMock = $this->createMock(ClassMetadata::class);
        $this->expectException(\Shopsys\FrameworkBundle\Component\Entity\Exception\UnexpectedTypeException::class);

        $entityNotNullableColumnsFinder = new EntityNotNullableColumnsFinder();
        $entityNotNullableColumnsFinder->getAllNotNullableColumnNamesIndexedByTableName([$classMetadataMock]);
    }
}
