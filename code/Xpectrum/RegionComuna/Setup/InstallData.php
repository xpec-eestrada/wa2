<?php
/**
* Copyright © 2017 Xpectrum. All rights reserved.
* See COPYING.txt for license details.
*/

namespace Xpectrum\RegionComuna\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Model\AttributeFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Config;


/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface{

    private $customerSetupFactory;
    private $_attrFactory;
    private $websiteFactory;
    protected $_eavConfig;
    private $_attrSetFactory;
    public function __construct(
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        AttributeFactory $attrFactory,
        WebsiteFactory $websiteFactory,
        Config $eavConfig,
        AttributeSetFactory $attrSetFactory
        )
    {
        $this->_attrFactory = $attrFactory;
        $this->_attrSetFactory = $attrSetFactory;
        $this->websiteFactory = $websiteFactory;
        $this->_eavConfig = $eavConfig;
        $this->customerSetupFactory = $customerSetupFactory;
    }
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
        /**
        * Install messages
        */
        $setup->startSetup();

        $data = [
            [
                'region_id'     => '2000',
                'country_id'    => 'CL',
                'code'          =>'AP',
                'default_name'  =>'XV - Arica y Parinacota'
            ],
            [
                'region_id'     => '2001',
                'country_id'    => 'CL',
                'code'          =>'TA',
                'default_name'  =>'I - Tarapacá'
            ],
            [
                'region_id'     => '2002',
                'country_id'    => 'CL',
                'code'          =>'AN',
                'default_name'  =>'II - Antofagasta'
            ],
            [
                'region_id'     => '2003',
                'country_id'    => 'CL',
                'code'          =>'AT',
                'default_name'  =>'III - Atacama'
            ],
            [
                'region_id'     => '2004',
                'country_id'    => 'CL',
                'code'          =>'CO',
                'default_name'  =>'IV - Coquimbo'
            ],
            [
                'region_id'     => '2005',
                'country_id'    => 'CL',
                'code'          =>'VA',
                'default_name'  =>'V - Valparaíso'
            ],
            [
                'region_id'     => '2006',
                'country_id'    => 'CL',
                'code'          =>'RM',
                'default_name'  =>'RM - Región Metropolitana'
            ],
            [
                'region_id'     => '2007',
                'country_id'    => 'CL',
                'code'          =>'LI',
                'default_name'  =>'VI - Rancagua'
            ],
            [
                'region_id'     => '2008',
                'country_id'    => 'CL',
                'code'          =>'ML',
                'default_name'  =>'VII - Maule'
            ],
            [
                'region_id'     => '2009',
                'country_id'    => 'CL',
                'code'          =>'BI',
                'default_name'  =>'VIII - Biobío'
            ],
            [
                'region_id'     => '2010',
                'country_id'    => 'CL',
                'code'          =>'AR',
                'default_name'  =>'XI - La Araucanía'
            ],
            [
                'region_id'     => '2011',
                'country_id'    => 'CL',
                'code'          =>'LR',
                'default_name'  =>'XIV - Los Ríos'
            ],
            [
                'region_id'     => '2012',
                'country_id'    => 'CL',
                'code'          =>'LL',
                'default_name'  =>'X - Los Lagos'
            ],
            [
                'region_id'     => '2013',
                'country_id'    => 'CL',
                'code'          =>'AY',
                'default_name'  =>'XI - Aysén'
            ],
            [
                'region_id'     => '2014',
                'country_id'    => 'CL',
                'code'          =>'MA',
                'default_name'  =>'XII - Magallanes'
            ]
        ];
        foreach ($data as $bind) {
            $setup->getConnection()
            ->insertForce($setup->getTable('directory_country_region'), $bind);
        }
        $data = array();
        $data = [
            [
                'locale'        => 'es_CL',
                'region_id'     => '2000',
                'name'          => 'XV - Arica y Parinacota'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2001',
                'name'          => 'I - Tarapacá'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2002',
                'name'          => 'II - Antofagasta'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2003',
                'name'          => 'III - Atacama'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2004',
                'name'          => 'IV - Coquimbo'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2005',
                'name'          => 'V - Valparaíso'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2006',
                'name'          => 'RM - Región Metropolitana'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2007',
                'name'          => 'VI - Rancagua'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2008',
                'name'          => 'VII - Maule'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2009',
                'name'          => 'VIII - Biobío'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2010',
                'name'          => 'XI - La Araucanía'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2011',
                'name'          => 'XIV - Los Ríos'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2012',
                'name'          => 'X - Los Lagos'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2013',
                'name'          => 'XI - Aysén'
            ],
            [
                'locale'        => 'es_CL',
                'region_id'     => '2014',
                'name'          => 'XII - Magallanes'
            ]
        ];
        foreach ($data as $bind) {
            $setup->getConnection()
            ->insertForce($setup->getTable('directory_country_region_name'), $bind);
        }
        $data = array();
        $data = [
            ['nombre' => 'Arica','idregion' => 2000 ],
            ['nombre' => 'Camarones','idregion' => 2000 ],
            ['nombre' => 'Putre','idregion' => 2000 ],
            ['nombre' => 'General Lagos','idregion' => 2000 ],
            ['nombre' => 'Iquique','idregion' => 2001 ],
            ['nombre' => 'Camiña','idregion' => 2001 ],
            ['nombre' => 'Colchane','idregion' => 2001 ],
            ['nombre' => 'Huara','idregion' => 2001 ],
            ['nombre' => 'Pica','idregion' => 2001 ],
            ['nombre' => 'Pozo Almonte','idregion' => 2001 ],
            ['nombre' => 'Alto Hospicio','idregion' => 2001 ],
            ['nombre' => 'Antofagasta','idregion' => 2002 ],
            ['nombre' => 'Mejillones','idregion' => 2002 ],
            ['nombre' => 'Sierra Gorda','idregion' => 2002 ],
            ['nombre' => 'Taltal','idregion' => 2002 ],
            ['nombre' => 'Calama','idregion' => 2002 ],
            ['nombre' => 'Ollagüe','idregion' => 2002 ],
            ['nombre' => 'San Pedro de Atacama','idregion' => 2002 ],
            ['nombre' => 'Tocopilla','idregion' => 2002 ],
            ['nombre' => 'María Elena','idregion' => 2002 ],
            ['nombre' => 'Copiapó','idregion' => 2003 ],
            ['nombre' => 'Caldera','idregion' => 2003 ],
            ['nombre' => 'Tierra Amarilla','idregion' => 2003 ],
            ['nombre' => 'Chañaral','idregion' => 2003 ],
            ['nombre' => 'Diego de Almagro','idregion' => 2003 ],
            ['nombre' => 'Vallenar','idregion' => 2003 ],
            ['nombre' => 'Alto del Carmen','idregion' => 2003 ],
            ['nombre' => 'Freirina','idregion' => 2003 ],
            ['nombre' => 'Huasco','idregion' => 2003 ],
            ['nombre' => 'La Serena','idregion' => 2004 ],
            ['nombre' => 'Coquimbo','idregion' => 2004 ],
            ['nombre' => 'Andacollo','idregion' => 2004 ],
            ['nombre' => 'La Higuera','idregion' => 2004 ],
            ['nombre' => 'Paiguano','idregion' => 2004 ],
            ['nombre' => 'Vicuña','idregion' => 2004 ],
            ['nombre' => 'Illapel','idregion' => 2004 ],
            ['nombre' => 'Canela','idregion' => 2004 ],
            ['nombre' => 'Los Vilos','idregion' => 2004 ],
            ['nombre' => 'Salamanca','idregion' => 2004 ],
            ['nombre' => 'Ovalle','idregion' => 2004 ],
            ['nombre' => 'Combarbalá','idregion' => 2004 ],
            ['nombre' => 'Monte Patria','idregion' => 2004 ],
            ['nombre' => 'Punitaqui','idregion' => 2004 ],
            ['nombre' => 'Río Hurtado','idregion' => 2004 ],
            ['nombre' => 'Valparaíso','idregion' => 2005 ],
            ['nombre' => 'Casablanca','idregion' => 2005 ],
            ['nombre' => 'Concón','idregion' => 2005 ],
            ['nombre' => 'Juan Fernández','idregion' => 2005 ],
            ['nombre' => 'Puchuncaví','idregion' => 2005 ],
            ['nombre' => 'Quilpué','idregion' => 2005 ],
            ['nombre' => 'Quintero','idregion' => 2005 ],
            ['nombre' => 'Villa Alemana','idregion' => 2005 ],
            ['nombre' => 'Viña del Mar','idregion' => 2005 ],
            ['nombre' => 'Isla  de Pascua','idregion' => 2005 ],
            ['nombre' => 'Los Andes','idregion' => 2005 ],
            ['nombre' => 'Calle Larga','idregion' => 2005 ],
            ['nombre' => 'Rinconada','idregion' => 2005 ],
            ['nombre' => 'San Esteban','idregion' => 2005 ],
            ['nombre' => 'La Ligua','idregion' => 2005 ],
            ['nombre' => 'Cabildo','idregion' => 2005 ],
            ['nombre' => 'Papudo','idregion' => 2005 ],
            ['nombre' => 'Petorca','idregion' => 2005 ],
            ['nombre' => 'Zapallar','idregion' => 2005 ],
            ['nombre' => 'Quillota','idregion' => 2005 ],
            ['nombre' => 'Calera','idregion' => 2005 ],
            ['nombre' => 'Hijuelas','idregion' => 2005 ],
            ['nombre' => 'La Cruz','idregion' => 2005 ],
            ['nombre' => 'Limache','idregion' => 2005 ],
            ['nombre' => 'Nogales','idregion' => 2005 ],
            ['nombre' => 'Olmué','idregion' => 2005 ],
            ['nombre' => 'San Antonio','idregion' => 2005 ],
            ['nombre' => 'Algarrobo','idregion' => 2005 ],
            ['nombre' => 'Cartagena','idregion' => 2005 ],
            ['nombre' => 'El Quisco','idregion' => 2005 ],
            ['nombre' => 'El Tabo','idregion' => 2005 ],
            ['nombre' => 'Santo Domingo','idregion' => 2005 ],
            ['nombre' => 'San Felipe','idregion' => 2005 ],
            ['nombre' => 'Catemu','idregion' => 2005 ],
            ['nombre' => 'Llaillay','idregion' => 2005 ],
            ['nombre' => 'Panquehue','idregion' => 2005 ],
            ['nombre' => 'Putaendo','idregion' => 2005 ],
            ['nombre' => 'Santa María','idregion' => 2005 ],
            ['nombre' => 'Rancagua','idregion' => 2007 ],
            ['nombre' => 'Codegua','idregion' => 2007 ],
            ['nombre' => 'Coinco','idregion' => 2007 ],
            ['nombre' => 'Coltauco','idregion' => 2007 ],
            ['nombre' => 'Doñihue','idregion' => 2007 ],
            ['nombre' => 'Graneros','idregion' => 2007 ],
            ['nombre' => 'Las Cabras','idregion' => 2007 ],
            ['nombre' => 'Machalí','idregion' => 2007 ],
            ['nombre' => 'Malloa','idregion' => 2007 ],
            ['nombre' => 'Mostazal','idregion' => 2007 ],
            ['nombre' => 'Olivar','idregion' => 2007 ],
            ['nombre' => 'Peumo','idregion' => 2007 ],
            ['nombre' => 'Pichidegua','idregion' => 2007 ],
            ['nombre' => 'Quinta de Tilcoco','idregion' => 2007 ],
            ['nombre' => 'Rengo','idregion' => 2007 ],
            ['nombre' => 'Requínoa','idregion' => 2007 ],
            ['nombre' => 'San Vicente','idregion' => 2007 ],
            ['nombre' => 'Pichilemu','idregion' => 2007 ],
            ['nombre' => 'La Estrella','idregion' => 2007 ],
            ['nombre' => 'Litueche','idregion' => 2007 ],
            ['nombre' => 'Marchihue','idregion' => 2007 ],
            ['nombre' => 'Navidad','idregion' => 2007 ],
            ['nombre' => 'Paredones','idregion' => 2007 ],
            ['nombre' => 'San Fernando','idregion' => 2007 ],
            ['nombre' => 'Chépica','idregion' => 2007 ],
            ['nombre' => 'Chimbarongo','idregion' => 2007 ],
            ['nombre' => 'Lolol','idregion' => 2007 ],
            ['nombre' => 'Nancagua','idregion' => 2007 ],
            ['nombre' => 'Palmilla','idregion' => 2007 ],
            ['nombre' => 'Peralillo','idregion' => 2007 ],
            ['nombre' => 'Placilla','idregion' => 2007 ],
            ['nombre' => 'Pumanque','idregion' => 2007 ],
            ['nombre' => 'Santa Cruz','idregion' => 2007 ],
            ['nombre' => 'Talca','idregion' => 2008 ],
            ['nombre' => 'Constitución','idregion' => 2008 ],
            ['nombre' => 'Curepto','idregion' => 2008 ],
            ['nombre' => 'Empedrado','idregion' => 2008 ],
            ['nombre' => 'Maule','idregion' => 2008 ],
            ['nombre' => 'Pelarco','idregion' => 2008 ],
            ['nombre' => 'Pencahue','idregion' => 2008 ],
            ['nombre' => 'Río Claro','idregion' => 2008 ],
            ['nombre' => 'San Clemente','idregion' => 2008 ],
            ['nombre' => 'San Rafael','idregion' => 2008 ],
            ['nombre' => 'Cauquenes','idregion' => 2008 ],
            ['nombre' => 'Chanco','idregion' => 2008 ],
            ['nombre' => 'Pelluhue','idregion' => 2008 ],
            ['nombre' => 'Curicó','idregion' => 2008 ],
            ['nombre' => 'Hualañé','idregion' => 2008 ],
            ['nombre' => 'Licantén','idregion' => 2008 ],
            ['nombre' => 'Molina','idregion' => 2008 ],
            ['nombre' => 'Rauco','idregion' => 2008 ],
            ['nombre' => 'Romeral','idregion' => 2008 ],
            ['nombre' => 'Sagrada Familia','idregion' => 2008 ],
            ['nombre' => 'Teno','idregion' => 2008 ],
            ['nombre' => 'Vichuquén','idregion' => 2008 ],
            ['nombre' => 'Linares','idregion' => 2008 ],
            ['nombre' => 'Colbún','idregion' => 2008 ],
            ['nombre' => 'Longaví','idregion' => 2008 ],
            ['nombre' => 'Parral','idregion' => 2008 ],
            ['nombre' => 'Retiro','idregion' => 2008 ],
            ['nombre' => 'San Javier','idregion' => 2008 ],
            ['nombre' => 'Villa Alegre','idregion' => 2008 ],
            ['nombre' => 'Yerbas Buenas','idregion' => 2008 ],
            ['nombre' => 'Concepción','idregion' => 2009 ],
            ['nombre' => 'Coronel','idregion' => 2009 ],
            ['nombre' => 'Chiguayante','idregion' => 2009 ],
            ['nombre' => 'Florida','idregion' => 2009 ],
            ['nombre' => 'Hualqui','idregion' => 2009 ],
            ['nombre' => 'Lota','idregion' => 2009 ],
            ['nombre' => 'Penco','idregion' => 2009 ],
            ['nombre' => 'San Pedro de la Paz','idregion' => 2009 ],
            ['nombre' => 'Santa Juana','idregion' => 2009 ],
            ['nombre' => 'Talcahuano','idregion' => 2009 ],
            ['nombre' => 'Tomé','idregion' => 2009 ],
            ['nombre' => 'Hualpén','idregion' => 2009 ],
            ['nombre' => 'Lebu','idregion' => 2009 ],
            ['nombre' => 'Arauco','idregion' => 2009 ],
            ['nombre' => 'Cañete','idregion' => 2009 ],
            ['nombre' => 'Contulmo','idregion' => 2009 ],
            ['nombre' => 'Curanilahue','idregion' => 2009 ],
            ['nombre' => 'Los Álamos','idregion' => 2009 ],
            ['nombre' => 'Tirúa','idregion' => 2009 ],
            ['nombre' => 'Los Ángeles','idregion' => 2009 ],
            ['nombre' => 'Antuco','idregion' => 2009 ],
            ['nombre' => 'Cabrero','idregion' => 2009 ],
            ['nombre' => 'Laja','idregion' => 2009 ],
            ['nombre' => 'Mulchén','idregion' => 2009 ],
            ['nombre' => 'Nacimiento','idregion' => 2009 ],
            ['nombre' => 'Negrete','idregion' => 2009 ],
            ['nombre' => 'Quilaco','idregion' => 2009 ],
            ['nombre' => 'Quilleco','idregion' => 2009 ],
            ['nombre' => 'San Rosendo','idregion' => 2009 ],
            ['nombre' => 'Santa Bárbara','idregion' => 2009 ],
            ['nombre' => 'Tucapel','idregion' => 2009 ],
            ['nombre' => 'Yumbel','idregion' => 2009 ],
            ['nombre' => 'Alto Biobío','idregion' => 2009 ],
            ['nombre' => 'Chillán','idregion' => 2009 ],
            ['nombre' => 'Bulnes','idregion' => 2009 ],
            ['nombre' => 'Cobquecura','idregion' => 2009 ],
            ['nombre' => 'Coelemu','idregion' => 2009 ],
            ['nombre' => 'Coihueco','idregion' => 2009 ],
            ['nombre' => 'Chillán Viejo','idregion' => 2009 ],
            ['nombre' => 'El Carmen','idregion' => 2009 ],
            ['nombre' => 'Ninhue','idregion' => 2009 ],
            ['nombre' => 'Ñiquén','idregion' => 2009 ],
            ['nombre' => 'Pemuco','idregion' => 2009 ],
            ['nombre' => 'Pinto','idregion' => 2009 ],
            ['nombre' => 'Portezuelo','idregion' => 2009 ],
            ['nombre' => 'Quillón','idregion' => 2009 ],
            ['nombre' => 'Quirihue','idregion' => 2009 ],
            ['nombre' => 'Ránquil','idregion' => 2009 ],
            ['nombre' => 'San Carlos','idregion' => 2009 ],
            ['nombre' => 'San Fabián','idregion' => 2009 ],
            ['nombre' => 'San Ignacio','idregion' => 2009 ],
            ['nombre' => 'San Nicolás','idregion' => 2009 ],
            ['nombre' => 'Treguaco','idregion' => 2009 ],
            ['nombre' => 'Yungay','idregion' => 2009 ],
            ['nombre' => 'Temuco','idregion' => 2010 ],
            ['nombre' => 'Carahue','idregion' => 2010 ],
            ['nombre' => 'Cunco','idregion' => 2010 ],
            ['nombre' => 'Curarrehue','idregion' => 2010 ],
            ['nombre' => 'Freire','idregion' => 2010 ],
            ['nombre' => 'Galvarino','idregion' => 2010 ],
            ['nombre' => 'Gorbea','idregion' => 2010 ],
            ['nombre' => 'Lautaro','idregion' => 2010 ],
            ['nombre' => 'Loncoche','idregion' => 2010 ],
            ['nombre' => 'Melipeuco','idregion' => 2010 ],
            ['nombre' => 'Nueva Imperial','idregion' => 2010 ],
            ['nombre' => 'Padre Las Casas','idregion' => 2010 ],
            ['nombre' => 'Perquenco','idregion' => 2010 ],
            ['nombre' => 'Pitrufquén','idregion' => 2010 ],
            ['nombre' => 'Pucón','idregion' => 2010 ],
            ['nombre' => 'Saavedra','idregion' => 2010 ],
            ['nombre' => 'Teodoro Schmidt','idregion' => 2010 ],
            ['nombre' => 'Toltén','idregion' => 2010 ],
            ['nombre' => 'Vilcún','idregion' => 2010 ],
            ['nombre' => 'Villarrica','idregion' => 2010 ],
            ['nombre' => 'Cholchol','idregion' => 2010 ],
            ['nombre' => 'Angol','idregion' => 2010 ],
            ['nombre' => 'Collipulli','idregion' => 2010 ],
            ['nombre' => 'Curacautín','idregion' => 2010 ],
            ['nombre' => 'Ercilla','idregion' => 2010 ],
            ['nombre' => 'Lonquimay','idregion' => 2010 ],
            ['nombre' => 'Los Sauces','idregion' => 2010 ],
            ['nombre' => 'Lumaco','idregion' => 2010 ],
            ['nombre' => 'Purén','idregion' => 2010 ],
            ['nombre' => 'Renaico','idregion' => 2010 ],
            ['nombre' => 'Traiguén','idregion' => 2010 ],
            ['nombre' => 'Victoria','idregion' => 2010 ],
            ['nombre' => 'Valdivia','idregion' => 2011 ],
            ['nombre' => 'Corral','idregion' => 2011 ],
            ['nombre' => 'Futrono','idregion' => 2011 ],
            ['nombre' => 'La Unión','idregion' => 2011 ],
            ['nombre' => 'Lago Ranco','idregion' => 2011 ],
            ['nombre' => 'Lanco','idregion' => 2011 ],
            ['nombre' => 'Los Lagos','idregion' => 2011 ],
            ['nombre' => 'Máfil','idregion' => 2011 ],
            ['nombre' => 'Mariquina','idregion' => 2011 ],
            ['nombre' => 'Paillaco','idregion' => 2011 ],
            ['nombre' => 'Panguipulli','idregion' => 2011 ],
            ['nombre' => 'Río Bueno','idregion' => 2011 ],
            ['nombre' => 'Puerto Montt','idregion' => 2012 ],
            ['nombre' => 'Calbuco','idregion' => 2012 ],
            ['nombre' => 'Cochamó','idregion' => 2012 ],
            ['nombre' => 'Fresia','idregion' => 2012 ],
            ['nombre' => 'Frutillar','idregion' => 2012 ],
            ['nombre' => 'Los Muermos','idregion' => 2012 ],
            ['nombre' => 'Llanquihue','idregion' => 2012 ],
            ['nombre' => 'Maullín','idregion' => 2012 ],
            ['nombre' => 'Puerto Varas','idregion' => 2012 ],
            ['nombre' => 'Castro','idregion' => 2012 ],
            ['nombre' => 'Ancud','idregion' => 2012 ],
            ['nombre' => 'Chonchi','idregion' => 2012 ],
            ['nombre' => 'Curaco de Vélez','idregion' => 2012 ],
            ['nombre' => 'Dalcahue','idregion' => 2012 ],
            ['nombre' => 'Puqueldón','idregion' => 2012 ],
            ['nombre' => 'Queilén','idregion' => 2012 ],
            ['nombre' => 'Quellón','idregion' => 2012 ],
            ['nombre' => 'Quemchi','idregion' => 2012 ],
            ['nombre' => 'Quinchao','idregion' => 2012 ],
            ['nombre' => 'Osorno','idregion' => 2012 ],
            ['nombre' => 'Puerto Octay','idregion' => 2012 ],
            ['nombre' => 'Purranque','idregion' => 2012 ],
            ['nombre' => 'Puyehue','idregion' => 2012 ],
            ['nombre' => 'Río Negro','idregion' => 2012 ],
            ['nombre' => 'San Juan de la Costa','idregion' => 2012 ],
            ['nombre' => 'San Pablo','idregion' => 2012 ],
            ['nombre' => 'Chaitén','idregion' => 2012 ],
            ['nombre' => 'Futaleufú','idregion' => 2012 ],
            ['nombre' => 'Hualaihué','idregion' => 2012 ],
            ['nombre' => 'Palena','idregion' => 2012 ],
            ['nombre' => 'Coihaique','idregion' => 2013 ],
            ['nombre' => 'Lago Verde','idregion' => 2013 ],
            ['nombre' => 'Aisén','idregion' => 2013 ],
            ['nombre' => 'Cisnes','idregion' => 2013 ],
            ['nombre' => 'Guaitecas','idregion' => 2013 ],
            ['nombre' => 'Cochrane','idregion' => 2013 ],
            ['nombre' => 'O\'Higgins','idregion' => 2013 ],
            ['nombre' => 'Tortel','idregion' => 2013 ],
            ['nombre' => 'Chile Chico','idregion' => 2013 ],
            ['nombre' => 'Río Ibáñez','idregion' => 2013 ],
            ['nombre' => 'Punta Arenas','idregion' => 2014 ],
            ['nombre' => 'Laguna Blanca','idregion' => 2014 ],
            ['nombre' => 'Río Verde','idregion' => 2014 ],
            ['nombre' => 'San Gregorio','idregion' => 2014 ],
            ['nombre' => 'Cabo de Hornos','idregion' => 2014 ],
            ['nombre' => 'Antártica','idregion' => 2014 ],
            ['nombre' => 'Porvenir','idregion' => 2014 ],
            ['nombre' => 'Primavera','idregion' => 2014 ],
            ['nombre' => 'Timaukel','idregion' => 2014 ],
            ['nombre' => 'Natales','idregion' => 2014 ],
            ['nombre' => 'Torres del Paine','idregion' => 2014 ],
            ['nombre' => 'Santiago','idregion' => 2006 ],
            ['nombre' => 'Cerrillos','idregion' => 2006 ],
            ['nombre' => 'Cerro Navia','idregion' => 2006 ],
            ['nombre' => 'Conchalí','idregion' => 2006 ],
            ['nombre' => 'El Bosque','idregion' => 2006 ],
            ['nombre' => 'Estación Central','idregion' => 2006 ],
            ['nombre' => 'Huechuraba','idregion' => 2006 ],
            ['nombre' => 'Independencia','idregion' => 2006 ],
            ['nombre' => 'La Cisterna','idregion' => 2006 ],
            ['nombre' => 'La Florida','idregion' => 2006 ],
            ['nombre' => 'La Granja','idregion' => 2006 ],
            ['nombre' => 'La Pintana','idregion' => 2006 ],
            ['nombre' => 'La Reina','idregion' => 2006 ],
            ['nombre' => 'Las Condes','idregion' => 2006 ],
            ['nombre' => 'Lo Barnechea','idregion' => 2006 ],
            ['nombre' => 'Lo Espejo','idregion' => 2006 ],
            ['nombre' => 'Lo Prado','idregion' => 2006 ],
            ['nombre' => 'Macul','idregion' => 2006 ],
            ['nombre' => 'Maipú','idregion' => 2006 ],
            ['nombre' => 'Ñuñoa','idregion' => 2006 ],
            ['nombre' => 'Pedro Aguirre Cerda','idregion' => 2006 ],
            ['nombre' => 'Peñalolén','idregion' => 2006 ],
            ['nombre' => 'Providencia','idregion' => 2006 ],
            ['nombre' => 'Pudahuel','idregion' => 2006 ],
            ['nombre' => 'Quilicura','idregion' => 2006 ],
            ['nombre' => 'Quinta Normal','idregion' => 2006 ],
            ['nombre' => 'Recoleta','idregion' => 2006 ],
            ['nombre' => 'Renca','idregion' => 2006 ],
            ['nombre' => 'San Joaquín','idregion' => 2006 ],
            ['nombre' => 'San Miguel','idregion' => 2006 ],
            ['nombre' => 'San Ramón','idregion' => 2006 ],
            ['nombre' => 'Vitacura','idregion' => 2006 ],
            ['nombre' => 'Puente Alto','idregion' => 2006 ],
            ['nombre' => 'Pirque','idregion' => 2006 ],
            ['nombre' => 'San José de Maipo','idregion' => 2006 ],
            ['nombre' => 'Colina','idregion' => 2006 ],
            ['nombre' => 'Lampa','idregion' => 2006 ],
            ['nombre' => 'Tiltil','idregion' => 2006 ],
            ['nombre' => 'San Bernardo','idregion' => 2006 ],
            ['nombre' => 'Buin','idregion' => 2006 ],
            ['nombre' => 'Calera de Tango','idregion' => 2006 ],
            ['nombre' => 'Paine','idregion' => 2006 ],
            ['nombre' => 'Melipilla','idregion' => 2006 ],
            ['nombre' => 'Alhué','idregion' => 2006 ],
            ['nombre' => 'Curacaví','idregion' => 2006 ],
            ['nombre' => 'María Pinto','idregion' => 2006 ],
            ['nombre' => 'San Pedro','idregion' => 2006 ],
            ['nombre' => 'Talagante','idregion' => 2006 ],
            ['nombre' => 'El Monte','idregion' => 2006 ],
            ['nombre' => 'Isla de Maipo','idregion' => 2006 ],
            ['nombre' => 'Padre Hurtado','idregion' => 2006 ],
            ['nombre' => 'Peñaflor','idregion' => 2006 ]
        ];
        foreach ($data as $bind) {
            $setup->getConnection()
            ->insertForce($setup->getTable('xpec_comunas'), $bind);
        }
        

        /* Attributo Drop Down List Comunas */
        $code_attribute     = 'xpec_comuna';
        $attrSet            = $this->_attrSetFactory->create();
        $entity_type        = $this->_eavConfig->getEntityType('customer_address');
        $entity_type_id     = $entity_type->getId();
        $attribute_set_id   = $entity_type->getDefaultAttributeSetId();
        $attribute_group_id = $attrSet->getDefaultGroupId($attribute_set_id);
        $customerSetup      = $this->customerSetupFactory->create(['setup' => $setup]);
        $order              = 101;
        
        

        $customerSetup->addAttribute('customer_address', $code_attribute,  array(
            "type"     => "int",
            "label"    => "Comunas",
            "input"    => "select",
            "visible"  => true,
            "required" => false,
            "system"   => 0,
            'user_defined' => true,
            'sort_order' => $order,
            "position" => $order,
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
            'source' => 'Xpectrum\RegionComuna\Model\Config\Source\OptionsComunas',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE

        ));

        $customerSetup->addAttributeToGroup(
            $entity_type_id,
            $attribute_set_id,
            $attribute_group_id,
            $code_attribute,
            '33'
        );

        $dropdownlist       = $customerSetup->getEavConfig()->getAttribute('customer_address', $code_attribute);$used_in_forms      = array();
        $used_in_forms[]    = "adminhtml_customer_address";
        $used_in_forms[]    = "customer_address_edit";
        $used_in_forms[]    = "customer_register_address";

        $dropdownlist->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", $order);
        $dropdownlist->save();
        $setup->endSetup();
        /* Attributo Drop Down List Comunas */

        /* Attributo Indicativo */
        $code_attribute     = 'xpec_prefijo_telefono';
        $attrSet            = $this->_attrSetFactory->create();
        $entity_type        = $this->_eavConfig->getEntityType('customer_address');
        $entity_type_id     = $entity_type->getId();
        $attribute_set_id   = $entity_type->getDefaultAttributeSetId();
        $attribute_group_id = $attrSet->getDefaultGroupId($attribute_set_id);
        $customerSetup      = $this->customerSetupFactory->create(['setup' => $setup]);
        $order              = 102;
        
        

        $customerSetup->addAttribute('customer_address', $code_attribute,  array(
            "type"     => "varchar",
            "label"    => "Indicativo",
            "input"    => "text",
            "visible"  => true,
            "required" => false,
            "system"   => 0,
            'user_defined' => true,
            'sort_order' => $order,
            "position" => $order,
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE

        ));

        $customerSetup->addAttributeToGroup(
            $entity_type_id,
            $attribute_set_id,
            $attribute_group_id,
            $code_attribute,
            '33'
        );

        $dropdownlist       = $customerSetup->getEavConfig()->getAttribute('customer_address', $code_attribute);$used_in_forms      = array();
        $used_in_forms[]    = "adminhtml_customer_address";
        $used_in_forms[]    = "customer_address_edit";
        $used_in_forms[]    = "customer_register_address";

        $dropdownlist->setData("used_in_forms", $used_in_forms)
            ->setData("is_used_for_customer_segment", true)
            ->setData("is_system", 0)
            ->setData("is_user_defined", 1)
            ->setData("is_visible", 1)
            ->setData("sort_order", $order);
        $dropdownlist->save();
        $setup->endSetup();
        /* Attributo Drop Down List Comunas */

    }
}