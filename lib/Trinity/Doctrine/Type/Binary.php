<?php
/*
 *  TRINITY FRAMEWORK <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 */

namespace Trinity\Doctrine\Type;
use \Doctrine\DBAL\Types\Type;
use \Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Getting things done right. It's a shame that there is no type
 * for storing binary data in Doctrine for the sake of database engine
 * compatibility, if it is widely known that the incompatibilities WERE,
 * ARE and WILL ALWAYS BE, no matter what Doctrine guys do. Why do I have
 * to suffer for someone else if MY database engine supports it pretty well?
 * Especially if THERE IS NO OTHER WAY TO STORE BINARY DATA THAN BLOBS?!
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Binary extends Type
{
    const BINARY = 'binary'; // modify to match your type name

	/**
	 * Look into Doctrine manual.
	 * 
	 * @param array $fieldDeclaration
	 * @param AbstractPlatform $platform
	 * @return string
	 */
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'BYTEA';
    } // end getSqlDeclaration();

	/**
	 * Look into Doctrine manual.
	 *
	 * @param string $value
	 * @param AbstractPlatform $platform
	 * @return string
	 */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    } // end convertToPHPValue();

	/**
	 * Look into Doctrine manual.
	 *
	 * @param string $value
	 * @param AbstractPlatform $platform
	 * @return string
	 */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value;
    } // end convertToDatabaseValue();

	/**
	 * Look into Doctrine manual.
	 *
	 * @return string
	 */
    public function getName()
    {
        return self::BINARY;
    } // end getName();

	/**
	 * Look into Doctrine manual.
	 *
	 * @return int
	 */
    public function getBindingType()
    {
        return \PDO::PARAM_LOB;
    } // end getBindingType();
} // end Blob;