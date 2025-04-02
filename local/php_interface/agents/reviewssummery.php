<?php

class ReviewsSummery
{
    public static function setupAgent()
    {
        CAgent::AddAgent(
            ReviewsSummery::class . '::Agent_ex_610();'
        );
    }

    public static function Agent_ex_610($lastExecTime = '')
    {
        $currTime = new \Bitrix\Main\Type\DateTime();
        $currTimeString = $currTime->ToString();

        if (\Bitrix\Main\Loader::includeModule('iblock')) {
//            $reviewIblock = \Bitrix\Iblock\Iblock::wakeUp(REVIEWS_IBLOCK_ID)->getEntityDataClass();
//            $changedReviews = $reviewIblock::query()
//                ->setSelect(['*'])
//                ->setFilter(['>TIMESTAMP_X' => $currTime->add('-1 day')])
//                ->queryCountTotal();

            $obChangedReviews = CIBlockElement::GetList(arFilter: ['IBLOCK_ID' => REVIEWS_IBLOCK_ID, '>TIMESTAMP_X' => $currTime->add('-1 day')], arSelectFields: ['ID']);
            if (!$obChangedReviews) {
                return;
            }
            $changedReviews = [];
            while ($changedReview = $obChangedReviews->GetNext()) {
                $changedReviews[] = $changedReview;
            }


            CEventLog::Add([
                'AUDIT_TYPE_ID' => 'ex2_610',
//                'LID' => 's1',
                'DESCRIPTION' => \Bitrix\Main\Localization\Loc::getMessage("REVIEWS_SUMMARY", [
                    '#EXEC_TIME#' => $lastExecTime,
                    '#COUNT#' => count($changedReviews)
                ]),
            ]);
        }

        return ReviewsSummery::class . "::Agent_ex_610('$currTimeString');";
    }
}
