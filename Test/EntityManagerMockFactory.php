<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\CoreBundle\Test;

class EntityManagerMockFactory
{
    /**
     * @param \PHPUnit_Framework_TestCase $test
     * @param callable                    $qbCallback
     * @param                             $fields
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    static function create(\PHPUnit_Framework_TestCase $test, \Closure $qbCallback, $fields)
    {
        $query = $test->getMockForAbstractClass('Doctrine\ORM\AbstractQuery', array(), '', false, true, true, array('execute'));
        $query->expects($test->any())->method('execute')->will($test->returnValue(true));

        $qb = $test->getMockBuilder('Doctrine\ORM\QueryBuilder')->disableOriginalConstructor()->getMock();
        $qb->expects($test->any())->method('select')->will($test->returnValue($qb));
        $qb->expects($test->any())->method('getQuery')->will($test->returnValue($query));
        $qb->expects($test->any())->method('where')->will($test->returnValue($qb));
        $qb->expects($test->any())->method('orderBy')->will($test->returnValue($qb));
        $qb->expects($test->any())->method('andWhere')->will($test->returnValue($qb));
        $qb->expects($test->any())->method('leftJoin')->will($test->returnValue($qb));

        $qbCallback($qb);

        $repository = $test->getMockBuilder('Doctrine\ORM\EntityRepository')->disableOriginalConstructor()->getMock();
        $repository->expects($test->any())->method('createQueryBuilder')->will($test->returnValue($qb));

        $metadata = $test->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $metadata->expects($test->any())->method('getFieldNames')->will($test->returnValue($fields));
        $metadata->expects($test->any())->method('getName')->will($test->returnValue('className'));

        $em = $test->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();
        $em->expects($test->any())->method('getRepository')->will($test->returnValue($repository));
        $em->expects($test->any())->method('getClassMetadata')->will($test->returnValue($metadata));

        return $em;
    }
}


