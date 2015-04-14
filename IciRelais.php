<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/

namespace IciRelais;

use IciRelais\Model\IcirelaisFreeshippingQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Exception\OrderException;
use Thelia\Install\Database;
use Thelia\Model\Country;
use Thelia\Module\AbstractDeliveryModule;

class IciRelais extends AbstractDeliveryModule
{
    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */

    const DOMAIN = 'icirelais';

    protected $request;
    protected $dispatcher;

    private static $prices = null;

    const JSON_PRICE_RESOURCE = "/Config/prices.json";

    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con->getWrappedConnection());

        $database->insertSql(null, array(__DIR__ . '/Config/thelia.sql'));
    }

    public static function getPrices()
    {
        if (null === self::$prices) {
            if (is_readable(sprintf('%s/%s', __DIR__, self::JSON_PRICE_RESOURCE))) {
                self::$prices = json_decode(
                    file_get_contents(sprintf('%s/%s', __DIR__, self::JSON_PRICE_RESOURCE)),
                    true
                );
            } else {
                self::$prices = null;
            }

        }

        return self::$prices;
    }

    /**
     * This method is called by the Delivery  loop, to check if the current module has to be displayed to the customer.
     * Override it to implements your delivery rules/
     *
     * If you return true, the delivery method will de displayed to the customer
     * If you return false, the delivery method will not be displayed
     *
     * @param Country $country the country to deliver to.
     *
     * @return boolean
     */
    public function isValidDelivery(Country $country)
    {
        $cartWeight = $this->getRequest()->getSession()->getCart()->getWeight();

        $areaId = $country->getAreaId();

        $prices = self::getPrices();

        /* check if Ici Relais delivers the asked area */
        if (isset($prices[$areaId]) && isset($prices[$areaId]["slices"])) {
            $areaPrices = $prices[$areaId]["slices"];
            ksort($areaPrices);

            /* check this weight is not too much */
            end($areaPrices);

            $maxWeight = key($areaPrices);
            if ($cartWeight <= $maxWeight) {
                return true;
            }
        }

        return false;
    }

    public static function getPostageAmount($areaId, $weight)
    {
        $freeshipping = IcirelaisFreeshippingQuery::create()->getLast();
        $postage=0;
        if (!$freeshipping) {
            $prices = self::getPrices();

            /* check if IciRelais delivers the asked area */
            if (!isset($prices[$areaId]) || !isset($prices[$areaId]["slices"])) {
                throw new OrderException(
                    "Ici Relais delivery unavailable for the chosen delivery country",
                    OrderException::DELIVERY_MODULE_UNAVAILABLE
                );
            }

            $areaPrices = $prices[$areaId]["slices"];
            ksort($areaPrices);

            /* check this weight is not too much */
            end($areaPrices);
            $maxWeight = key($areaPrices);
            if ($weight > $maxWeight) {
                throw new OrderException(
                    sprintf("Ici Relais delivery unavailable for this cart weight (%s kg)", $weight),
                    OrderException::DELIVERY_MODULE_UNAVAILABLE
                );
            }

            $postage = current($areaPrices);

            while (prev($areaPrices)) {
                if ($weight > key($areaPrices)) {
                    break;
                }

                $postage = current($areaPrices);
            }
        }

        return $postage;
    }

    public function getPostage(Country $country)
    {
        $cartWeight = $this->getRequest()->getSession()->getCart()->getWeight();

        $postage = self::getPostageAmount(
            $country->getAreaId(),
            $cartWeight
        );

        return $postage;
    }
}
