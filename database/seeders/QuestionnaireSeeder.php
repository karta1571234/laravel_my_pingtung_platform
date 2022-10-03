<?php

namespace Database\Seeders;

use App\Models\Questionnaire;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionnaireSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Questionnaire::create(['question' => '請問您的姓名：', 'option' => '[]', 'input_type' => 'text_string', 'tips' => '']);
        Questionnaire::create(['question' => '請問您的身分證字號：', 'option' => '[]', 'input_type' => 'text_string', 'tips' => '']);
        Questionnaire::create(['question' => '請問您的出生年月日：', 'option' => '[]', 'input_type' => 'date', 'tips' => '']);
        Questionnaire::create(['question' => '請問您的性別：', 'option' => '["男","女"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您有無手機：', 'option' => '["有,號碼:","無"]', 'input_type' => 'text_string', 'tips' => '']);
        Questionnaire::create(['question' => '請問您家裡有無市用電話：', 'option' => '["有,號碼:","無"]', 'input_type' => 'text_string', 'tips' => '']);
        Questionnaire::create(['question' => '請問您現在居住於屏東縣哪一鄉鎮？', 'option' => '["屏東市","高樹鄉","鹽埔鄉","竹田鄉","內埔鄉","萬丹鄉","潮州鎮","萬巒鄉","東港鎮","獅子鄉","恆春鎮","其他"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您是否具原住民身分？', 'option' => '["無","有"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您的族群背景？', 'option' => '["閩南人/漢人","客家人","原住民","其他"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您的宗教信仰？', 'option' => '["無","拜拜(一般民間信仰或拜祖先)","佛教","基督教","天主教","一貫道","道教","其他，請說明:"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您的最高教育程度？', 'option' => '["不識字","私塾，自學或小學肄業等識字者","小學","國(初)中","高中(職)","專科(五專)","大學(專)學院","研究所(含以上)"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您目前的婚姻狀況？', 'option' => '["單身(從未結婚)","已婚同居","離婚或分居","配偶已過世","其他,請說明:"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您有與家人同住？', 'option' => '["沒有","有"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您現在還有在從事有收入的工作嗎？', 'option' => '["沒有","有"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您現在是否有擔任志工？', 'option' => '["沒有","有"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您現在有參加相關的社會活動嗎？', 'option' => '["沒有","有"]', 'input_type' => 'radiobox', 'tips' => '※長者參與的社會活動包含：社區照顧關懷據點、文化健康站、社區大學、樂齡學習中心、樂齡大學、部落大學、松年大學、長青學苑、老人會、到教堂禮拜或寺廟拜拜等。']);
        Questionnaire::create(['question' => '請問您是否為低收∕中低收入戶？', 'option' => '["不是","是"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您目前是否有身心障礙證明？', 'option' => '["不是","是"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您的身高:', 'option' => '[]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您的體重:', 'option' => '[]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您目前是每天吸菸、有時吸菸還是都沒有吸菸？', 'option' => '["從來沒抽","以前有，現在沒有","有時吸菸","每天吸菸"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '請問您是否有喝酒？', 'option' => '["從來不喝","社交飲酒","規律性飲酒"]', 'input_type' => 'radiobox', 'tips' => '※社交飲酒為「偶爾喝、有朋友來、聚會、有事慶祝等」；規律飲酒為「有習慣喝」。']);
        Questionnaire::create(['question' => '請問您是否有慢性疾病史？（可複選）', 'option' => '["沒有","高血壓","糖尿病","高血脂症","心臟病","腦中風","腎臟病","精神疾病","慢性阻塞性肺部疾病（COPD）","癌症","其他"]', 'input_type' => 'checkbox', 'tips' => '']);
        Questionnaire::create(['question' => '在過去一個月內，請問您有沒有在運動？', 'option' => '["都沒有運動【請直接跳答第三部份】","因身體無法運動【請直接跳答第三部份】","有運動（續答第七題）"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '在過去一個月內，您平均一個星期做幾天運動？', 'option' => '["每周一天","每周二天","每周三天","每周四天","每周五天","每周六天","每周七天"]', 'input_type' => 'radiobox', 'tips' => '※持續十分鐘以上才算是運動，工作上的勞動不算（例如下田、搬貨或做家務等）。']);
        Questionnaire::create(['question' => '每天花多少分鐘運動？', 'option' => '["不知道","平均"]', 'input_type' => 'radiobox', 'tips' => '']);
        Questionnaire::create(['question' => '過去一個月內，請問您最常做的是哪一些運動呢？（可複選）', 'option' => '["散步(健走)","騎單車","慢跑","爬山","游泳","體操、舞蹈類運動及傳統武術","球類運動","健身器材","其他"]', 'input_type' => 'checkbox', 'tips' => '']);
        Questionnaire::create(['question' => '在過去一個月內，請問您每次運動時會不會流汗？會不會喘？（以最常運動的項目為準）', 'option' => '["很輕鬆，不會喘不會流汗","會流汗，但不會喘","不會流汗，但會喘","會流汗，也會喘"]', 'input_type' => 'radiobox', 'tips' => '']);
    }
}
