<?php

namespace Fatchip\RestAPI\Controller;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class RestUserGroup extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    public function render()
    {

        $request = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\Request::class);
        $id = $request->getRequestParameter('id');
        $name = $request->getRequestParameter('name');
        $username = $request->getRequestParameter('username');
        $password = $request->getRequestParameter('password');
        $sRequestMethod = $this->fcGetRequestMethod();
        $_SERVER['PHP_AUTH_USER'] = $username;
        $_SERVER['PHP_AUTH_PW'] = $password;

        if (!$this->isAuthenticated()) {
            header('HTTP/1.1 401 Unauthorized');
            echo '401 Unauthorized';
            exit();
        }

        if ($sRequestMethod === 'GET') {
            $this->handleGetRequest($id);
        } elseif ($sRequestMethod === 'POST') {
            $this->handlePostRequest($id, $name);
        } elseif ($sRequestMethod === 'PUT') {
            $this->handlePutRequest($id, $name);
        } elseif ($sRequestMethod === 'DELETE') {
            $this->handleDeleteRequest($id);
        }

        return 'test_form.tpl';
    }

    private function isAuthenticated()
    {
        $username = $_SERVER['PHP_AUTH_USER'] ?? '';
        $password = $_SERVER['PHP_AUTH_PW'] ?? '';

        $sQuery = "SELECT OXPASSWORD, oxrights FROM oxuser WHERE OXUSERNAME = '".$username."'";
        $aResults = $this->fcExecuteQueryAndReturnResult($sQuery);
        $sHashedPassowrd = $aResults[0]['OXPASSWORD'];
        $sUserRights = $aResults[0]['oxrights'];
        $blPasswordVerification = password_verify($password, $sHashedPassowrd);
        if ($sUserRights === 'malladmin' && $blPasswordVerification) {
            return true;
        }

        return false;
    }

    private function fcGetRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    private function handleGetRequest($id)
    {
        if ($id === null) {
            $sQuery = "SELECT oxid, oxtitle FROM oxgroups";
            $aResults = $this->fcExecuteQueryAndReturnResult($sQuery);
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            foreach ($aResults as $aResult) {
                echo json_encode($aResult, JSON_PRETTY_PRINT);
            }
            return;
        }

        $sQuery = "SELECT oxid, oxtitle FROM oxgroups WHERE oxid = '".$id."'";
        $aResults = $this->fcExecuteQueryAndReturnResult($sQuery);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        echo json_encode($aResults, JSON_PRETTY_PRINT);
    }

    private function handlePostRequest($id, $name)
    {
        $sQuery = "UPDATE oxgroups SET oxtitle = '".$name."' WHERE oxid = '".$id."'";
        $this->fcExecuteQueryAndReturnResult($sQuery);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $aResponse = [
            'message' => 'POST request successful',
            'POST data' => [
                "oxid" => $id,
                "oxtitle" => $name
            ],
        ];
        echo json_encode($aResponse, JSON_PRETTY_PRINT);
    }

    private function handlePutRequest($id, $name)
    {
        $sQuery = "INSERT INTO oxgroups (oxid, oxtitle) VALUES ('".$id."', '".$name."') ";
        $this->fcExecuteQueryAndReturnResult($sQuery);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $response = [
            'message' => 'PUT request successful',
            'PUT data' => [
                'oxid' => $id,
                'oxtitle' => $name,
            ],
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    private function handleDeleteRequest($id)
    {
        $sQuery = "DELETE FROM oxgroups WHERE oxid = '".$id."' ";
        $this->fcExecuteQueryAndReturnResult($sQuery);
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $response = [
            'message' => 'DELETE request successful',
        ];
        echo json_encode($response, JSON_PRETTY_PRINT);
    }

    private function fcExecuteQueryAndReturnResult($sQuery)
    {
        $oQueryBuilder = ContainerFactory::getInstance()->getContainer()->get(QueryBuilderFactoryInterface::class)->create();
        $oConnection = $oQueryBuilder->getConnection();
        $result = $oConnection->executeQuery($sQuery);
        return $result->fetchAll();
    }
}