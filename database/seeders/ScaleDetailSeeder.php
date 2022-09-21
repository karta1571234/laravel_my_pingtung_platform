<?php

namespace Database\Seeders;

use App\Models\ScaleDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScaleDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //AD8
        ScaleDetail::create([
            'scale_order_id' => 1, 'question' => '1.如果您在07:59起床時發現您08:00時與人有重要約會，您該怎麼辦? ※請依據長者的回應，觀察有無出現判斷力上的困難。', 'option' => '["有改變","不是,沒有改變","不知道"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 1, 'question' => '2.您以前常做的事（例如：逛街、下棋、打牌、看電視連續劇或是政論節目等活動），現在是否越來越少做了？ ※請依據長者的回應，觀察有無出現對活動和嗜好的興趣降低。', 'option' => '["有改變","不是,沒有改變","不知道"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 1, 'question' => '3.有沒有人說您最近會重覆說同樣的事情？ ※請依據長者的回應，觀察有無出現重複相同的問題、故事和陳述。', 'option' => '["有改變","不是,沒有改變","不知道"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 1, 'question' => '4.請在二題選項中選一題，詢問長者： (1)您現在看電視時可以使用遙控器轉台嗎？ (2)您現在會用洗衣機洗衣服或是微波爐熱食物嗎？ ※若表示均不用上述工具，請進一步詢問是否是因不會使用而不使用？ ※請依據長者的回應，觀察有無出現對於學習工具、設備有困難。', 'option' => '["有改變","不是,沒有改變","不知道"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 1, 'question' => '5.今天是民國幾年幾月（幾日）？ ※至少要正確回應幾年幾月，若長者答不出確切幾日，圈選「0」。 ※請依據長者的回應，觀察有無出現會忘記正確的年份和月份。', 'option' => '["有改變","不是,沒有改變","不知道"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 1, 'question' => '6.請在二題選項中選一題，詢問長者： (1)如果您帶1000元到市場買東西，一共花了265元，還剩下多少錢? 以及「100+7等於多少」或「20-3等於多少? (2)您現在可不可以自己去銀行提款、繳費、上街買東西？ ※請依據長者的回應，觀察有無出現在處理複雜的財務上有困難。 ※請先詢問長者有無上街買東西，再詢問(100-7)、(20-3)兩題算數題，以算數答案的正確性，為判別有無改變的依據。', 'option' => '["有改變","不是,沒有改變","不知道"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 1, 'question' => '7.偶爾會忘了和別人約定的時間或該去的地方（例如：與老朋友有約，或是忘記去教堂）？ ※請依據長者的回應，觀察有無出現記住約會的時間有困難。', 'option' => '["有改變","不是,沒有改變","不知道"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 1, 'question' => '8.您會不會常找不到貴重東西（如錢或重要證件）或覺得東西不見？ ※請依據長者的回應，觀察有無出現有持續的思考和記憶方面的問題。', 'option' => '["有改變","不是,沒有改變","不知道"]'
        ]);

        //GDS-15
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '1.基本上，您對您的生活滿意嗎？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '2.您是否減少很多的活動和興趣的事？ ', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '3.您是否覺得您的生活很空虛？ ', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '4.您是否常常感到厭煩？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '5.您是否大部份時間精 ？ ', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '6.您是否害怕將有不幸的事情發生在您身上嗎？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '7.您是否大部份的時間都感到快樂？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '8.您是否常常感到無論做什麼事，都沒有用？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '9.您是否比較喜歡待在家裡而較不喜歡外出及不喜歡做新的事？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '10.您是否覺得現在有記憶力不好的困擾？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '11.您是否覺得現在還能活著是很好的事？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '12.您是否感覺您現在活得很沒有價值？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '13.您是否覺得精力很充沛？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '14.您是否覺得您現在的情況是沒有希望的？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 2, 'question' => '15.您是否覺得大部份的人都比您幸福？', 'option' => '["是","否"]']);

        //ICOPE
        ScaleDetail::create(['scale_order_id' => 3, 'question' => '1-1.記憶力:說出三項物品(鉛筆、汽車、書)，請長者重複，並記住。第3題後再詢問一次。', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 3, 'question' => '1-2.定向力：詢問長者「今天的日期？」（含年月日），長者回答是否正確？', 'option' => '["是","否"]']);
        ScaleDetail::create([
            'scale_order_id' => 3, 'question' => '1-3.定向力：詢問長者「您現在在哪裡？」，長者回答是否正確？ ※完成第二題及第三題後，請再次詢問長者，第1題記憶力的3項物品。', 'option' => '["是","否"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 3, 'question' => '2-1.椅子起身測試:12秒內，可以雙手抱胸，連續起立坐下 5 次。 ※請訪員先示範動作給長者看，並把計時用具準備好。', 'option' => '["是","否"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 3, 'question' => '3-1.過去三個月，您的體重是否在無意中減輕了 3 公斤以上？ ※若長者表示不清楚，可以進一步請他回想衣服、褲頭是否有變得較為寬鬆？', 'option' => '["是","否"]'
        ]);
        ScaleDetail::create(['scale_order_id' => 3, 'question' => '3-2.過去三個月，您是否曾經食慾不振？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 3, 'question' => '4-1.您的眼睛看遠、看近或閱讀是否有困難？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 3, 'question' => '4-2.你過去一年是否有「曾」接受眼睛檢查？', 'option' => '["是","否"]']);
        ScaleDetail::create([
            'scale_order_id' => 3, 'question' => '5-1.請跟著我唸 6、1、9。 ※請訪員用氣音測試，若長者未能正確複誦，再測一次2、5、7，仍未能正確複誦，即圈選「否」。 ※請訪員站在長者面前一個手臂距離，先測試右耳、再測試左耳。（任一耳聽不清楚，即可結束測試，圈選「否」。）', 'option' => '["是","否"]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 3, 'question' => '6-1.過去兩週，您是否常感到厭煩（心煩或台語「阿雜」）或沒有希望？ ※詢問長者是否常感到憂鬱／心情低落的情緒。', 'option' => '["是","否"]'
        ]);
        ScaleDetail::create(['scale_order_id' => 3, 'question' => '6-2.過去兩週，您是否減少很多的活動和興趣的事？', 'option' => '["是","否"]']);
        ScaleDetail::create([
            'scale_order_id' => 3, 'question' => '7-1.您每天使用的藥物是否10種(含)以上(包含中藥)?(保健食品不包含在內) ※詢問長者一天吃幾次藥，每次藥量為幾顆?若加總超過10顆藥，圈選「是」。', 'option' => '["是","否"]'
        ]);
        ScaleDetail::create(['scale_order_id' => 3, 'question' => '7-2.您服用的藥品是否包含止痛藥、幫助睡眠用藥等？', 'option' => '["是","否"]']);
        ScaleDetail::create(['scale_order_id' => 3, 'question' => '7-3.您是否因為服用藥品而發生平衡感改變、睏倦、眩暈、低血壓或口乾舌燥等症狀？', 'option' => '["是","否"]']);
        ScaleDetail::create([
            'scale_order_id' => 3, 'question' => '8-1.現在最困擾您的健康問題是什麼？（簡述長者最困擾的一項健康問題） 請說明：', 'option' => '[]'
        ]);
        ScaleDetail::create([
            'scale_order_id' => 3, 'question' => '8-2.您最擔心這個健康問題影響到生活上的什麼事？（簡述長者最擔心的一項事情） 請說明：', 'option' => '[]'
        ]);
    }
}
