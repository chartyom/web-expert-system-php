<?php
namespace engine\components;


class AdviceAnalyzingTools
{
    //Гласные
    private static $VOWEL = '/аеиоуыэюя/iu';

    //Совершенный вид
    private static $PERFECTIVEGERUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/iu';

    //Возвратные глаголы
    private static $REFLEXIVE = '/(с[яь])$/iu';

    //ПРИЛАГАТЕЛЬНОЕ
    private static $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|ему|ому|их|ых|ую|юю|ая|яя|ою|ею)$/iu';

    //ПРИЧАСТИЕ
    private static $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/iu';

    //ГЛАГОЛ
    private static $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ен|ило|ыло|ено|ят|ует|уют|ит|ыт|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/iu';

    //СУЩЕСТВИТЕЛЬНОЕ
    private static $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|я)$/iu';

    //Метод разделения слов с помощью глассных
    private static $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/iu';

    //Словообразование
    public static $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/iu';


    //ЧАСТИЦЫ, частица 'не' не включена, так как используется при отрицании.
    private static $PARTICLES = '/\b(разве|неужели|вот|вон|так|именно|как раз|почти|чуть|точно|точь-в-точь|ровно|только|лишь|исключительно|почти|единственно|что за|ну и|вряд ли|едва ли|даже|же|ни|ну|уж|ведь|всё-таки|лишь|только|бы|под|над|ничего|к|за)\b/iu';
    private static $PARTICLES_ADDITION = '/\b(не)\b/iu';

    //СОЮЗЫ
    private static $UNIONS = '/\b(что|чтобы|что бы|и|а|но|когда|если|в то время как|потому что|так как|да|ни|не только|но и|так и|однако|же|зато|или|либо|то|не то|то ли|также|тоже|едва|лишь только|с тех пор как|оттого что|вследствие того что|для того чтобы|так что|ежели|коли|хотя|пускай|несмотря на то что|как будто|будто|словно|как|у)\b/iu';

    //МЕСТОИМЕНИЯ
    private static $PRONOUN = '/\b(я|ты|он|она|оно|мы|вы|они|себя|мой|твой|свой|ваш|наш|его|её|их|кто|что|какой|чей|где|который|откуда|сколько|каковой|каков|зачем|когда|тот|этот|такой|таков|сей|там|всякий|каждый|сам|самый|любой|иной|другой|весь|никто|ничто|никакой|ничей|некого|нечего|незачем)\b/iu';

    //ЦИФРЫ
    private static $NUMBERS = '/[0-9]+/iu';

    //ЗНАКИ ПРЕПИНАНИЯ
    private static $PUNCTUATION_MARKS = '/[^\d\w ]+/iu';

    //Шаблоны поиска
    private static $patterns = [
        '/(([3|4]{1}[8-9]{1})((,|\.| и )[0-9]{1})?)|((37)((,|\.| и )[5-9]{1})?)/iu'=>'высокая температура',
        '/((37)((,|\.| и )[0-4]{1})?)|((36)((,|\.| и )[0-9]{1})?)|((35)((,|\.| и )[4-9]{1})?)/iu'=>'нормальная температура',
        '/((35)((,|\.| и )[0-4]{1})?)|((3[0-4]{1})((,|\.| и )[0-9]{1})?)/iu'=>'низкая температура',
    ];

    /**
     * Очищает сообщение от всего лишнего: заков припинания, неиспользуемых цифр, частиц, союзов, местоимений
     *
     * @param $message
     *
     * @return mixed|string
     */
    public static function messageClean($message){

        //перевод строки в нижний регистр
        $message1 = mb_strtolower($message);

        //очищает частицы
        $message2 = preg_replace(self::$PARTICLES, '', $message1);

        //очищает местоимения
        $message3 = preg_replace(self::$PRONOUN, '', $message2);

        //очищает союзы
        $message4 = preg_replace(self::$UNIONS, '', $message3);

        //очищает цифры
        $message5 = preg_replace(self::$NUMBERS, '', $message4);

        //очищает знаки препинания
        $message6 = preg_replace(self::$PUNCTUATION_MARKS, ' ', $message5);

        //Замена двойных пробелов одинарными
        $message7 = preg_replace('/[ ]+/', ' ', $message6);

        //очистка от пробелов с краёв
        return trim($message7);
    }
    
    
    /**
     * Разделитель текста
     * Разделяет по пробелам
     * удаляет повторяющиеся слова
     * @param $message
     *
     * @return array
     */
    public static function getDelimiter($message){

        //разделение сообщения на предложения
        /*$message_point = explode(".", $message);*/

        //разделение предложения на составные части
        /*foreach ($message_point AS $key=>$value){
            $message_point[$key] = explode(",", $value);
        }*/

        /*foreach ($message_point AS $key1=>$value1){
            if(is_array($value1)){
                foreach ($value1 AS $key2=>$value2){
                    $messageWords[$key1][$key2] = explode(" ", $value2);
                }
            } else{
                $messageWords[$key1] = explode(" ", $value1);
            }
        }*/
        $messageWords = explode(' ', $message);
        /*
         * Длина массива
         * Начало с 0
        */
        /*$messageWordsCount = count($messageWords)-1;
        //Проверка на одинаковые слова
        for ($i=0; $i<$messageWordsCount; $i++){
            for ($x=0; $x<$messageWordsCount; $x++){
                if($i!=$x){
                    if(self::getSimilarity($messageWords[$i],$messageWords[$x]) > 99){
                        unset($messageWords[$x]);
                    }
                }
            }
        }*/


        foreach ($messageWords AS $key=>$value){
            if (!preg_match(self::$RVRE, $value, $p)) break;
            $start = $p[1];
            $RV = $p[2];
            if (!empty($RV)) {
                if (!self::replace($RV, self::$PERFECTIVEGERUND, '')) {
                    self::replace($RV, self::$REFLEXIVE, '');
                    //создание словосочетания из прилагательного и последующего слова
                    if (preg_match (self::$ADJECTIVE, $RV)) {
                        $next_key = $key + 1;
                        if (!empty($messageWords[$next_key])) {
                            $messageWords[$key] = $value . " " . $messageWords[$next_key];
                            unset($messageWords[$next_key]);
                        };
                    }
                }
            } else {
                /*Удаляет отрицания (не болит, не готов, ...)*/
                if (preg_match(self::$PARTICLES_ADDITION, $value)) {
                    $next_key = $key + 1;
                    if (!empty($messageWords[$next_key])) {
                        $messageWords[$key] = $value . " " . $messageWords[$next_key];
                        unset($messageWords[$next_key]);//удаление следующего элемента массива
                        //unset($messageWords[$key]);//удаление текущего элемента массива
                    };
                }
            }
        }


        foreach ($messageWords AS $key1=>$value1){
            foreach ($messageWords AS $key2=>$value2){
                if($key1!=$key2){
                    if(self::getSimilarity($value1,$value2) == 100){
                        unset($messageWords[$key2]);
                    }
                }
            }
        }


        return $messageWords;
    }


    /**
     * Осуществляет поиск словосочетаний по шаблону
     *
     * @param $message
     *
     * @return array
     */
    public static function getExtra($message){
        $result = [];
        foreach (self::$patterns AS $pattern=>$value){
            preg_match_all($pattern, $message, $p, PREG_PATTERN_ORDER);
            if (!empty($p[0])) {
                $result[] = self::getWordBase($value);
            };
        }
        return $result;
    }

    /**
     * @param $subject
     * @param $pattern
     * @param $replacement
     *
     * @return bool
     */
    private static function replace(&$subject, $pattern, $replacement)
    {
        $orig = $subject;
        $subject = preg_replace($pattern, $replacement, $subject);
        return $orig !== $subject;
    }

    /**
     * @param $subject
     * @param $pattern
     *
     * @return int
     */
    private static function match($subject, $pattern)
    {
        return preg_match($pattern, $subject);
    }

    /**
     * @param $message
     *
     * @return array
     */
    public static function getMessageBase($message){

        $messageListWordBase = [];

        $messageClean = self::messageClean($message);

        $messageWords = self::getDelimiter($messageClean);

        foreach ($messageWords AS $key1=>$value1){
            if(is_array($value1)){
                foreach ($value1 AS $key2=>$value2){
                    if(is_array($value2)){
                        foreach ($value2 AS $key3=>$value3){
                            $messageListWordBase[$key1][$key2][$key3] = self::getWordBase($value3);
                        }
                    } else{
                        $messageListWordBase[$key1][$key2] = self::getWordBase($value2);
                    }
                }
            } else{
                $messageListWordBase[$key1] = self::getWordBase($value1);
            }
        }

        $extra = self::getExtra($message);

        if($extra){
            foreach ($extra AS $value){
                $messageListWordBase[]=$value;
            }
        }

        return $messageListWordBase;
    }

    /**
     * Операция сравнения
     * Выводит результат сходства двух слов в процентах(%)
     *
     * @param $firstWord
     * @param $secondWord
     *
     * @return float
     */
    public static function getSimilarity($firstWord,$secondWord){
        $secondWord_count = mb_strlen($secondWord,'UTF-8');
        $firstWord_count = mb_strlen($firstWord,'UTF-8');
        $coincidence = 0;
        $max = $firstWord_count;
        if($secondWord_count > $firstWord_count){
            $max = $secondWord_count;
        }
        $firstWord = mb_convert_encoding($firstWord, 'ISO-8859-5', 'UTF-8');
        $secondWord = mb_convert_encoding($secondWord, 'ISO-8859-5', 'UTF-8');

        for($i = 0; $i <= $max-1; $i++){
            if(!empty($secondWord[$i]) && !empty($firstWord[$i])){
                if($firstWord[$i]==$secondWord[$i]){
                    $coincidence++;
                }
            }
        }

        $result = $coincidence / ($max / 100);

        return round($result,2);
    }


    /**
     * @param $word
     *
     * @return mixed|string
     */
    public static function getWordBase($word)
    {
        //Описание
        //http://snowball.tartarus.org/algorithms/russian/stemmer.html
        /*
            Разметка слова на части RV, R1, R2.

            RV  - это часть слова, идущая после первой гласной или пустая строка, если в нем нет гласных.

            R1 - это часть слова после первой согласной, следующей за гласной или пустая строка, если такой нет.

            R2 - это часть слова после первой согласной, следующей за гласной в R1, или пустая строка, если такой нет.

            Например:

               п р о т и в о е с т е с т в е н н о м
                    |<------       RV        ------>|
                      |<-----       R1       ------>|
                          |<-----     R2     ------>|

            Далее все шаги выполняются только над RV-частью слова.
            При поиске окончания в классе всегда выбирается самое длинное.

            Шаг 1: Ищем окончания PERFECTIVE GERUND. Если найдено, удаляем его и это будет конец шага 1, иначе ищем и удаляем окончания REFLEXIVE, затем ищем по очереди окончания ADJECTIVAL, VERB, NOUN. Если найдено, то удаляем их и завершаем шаг 1.

            Шаг 2: Если слово окончаивается на "и", удаляем его.

            Шаг 3: Ищем окончание DERIVATIONAL в отрезке R2 (т.е. все окончание должно лежать в R2), и если найдено, удаляем.

            Шаг 4: Убираем двойное "н" или, если слово имеет SUPERLATIVE окончание, удаляем его и убираем двойное "н", или, если оканчивается на "ь" (мягкий знак), то удаляем его.

            ---
            Классы окончаний

            PERFECTIVE GERUND:

               группа 1:   в вши вшись

               группа 2:   ив ивши ившись ыв ывши ывшись

               окончания из группы 1 должны следовать за "а" или "я"

            ADJECTIVE:

               ее ие ые ое ими ыми ей ий ый ой ем им ым ом его ого ему ому их ых ую юю ая яя ою ею

            PARTICIPLE:

               группа 1:   ем нн вш ющ щ

               группа 2:   ивш ывш ующ

            окончания из группы 1 должны следовать за "а" или "я"

            REFLEXIVE:

               ся сь

            VERB:

               группа 1: ла на ете йте ли й л ем н ло но ет ют ны ть ешь нно

               группа 2: ила ыла ена ейте уйте ите или ыли ей уй ил ыл им ым ен ило ыло   ено ят ует уют ит ыт ены ить ыть ишь ую ю

            окончания из группы 1 должны следовать за "а" или "я"

            NOUN:

               а ев ов ие ье е иями ями ами еи ии и ей ей ой ий й иям ям ием ем ам ом о у ах иях ях ы ь ию ью ю ия ья я

            SUPERLATIVE:

               ейш ейше

            DERIVATIONAL:

               ост ость
         * */

        $word = str_replace( 'ё', 'е', $word);
        $stem = $word;
        do {

            //разделение слова на три составные части
            if (!preg_match(self::$RVRE, $word, $p)) break;
            $start = $p[1];
            $RV = $p[2];
            if (!$RV) break;

            # Step 1
            if (!self::replace($RV, self::$PERFECTIVEGERUND, '')) {
                self::replace($RV, self::$REFLEXIVE, '');

                if (self::replace($RV, self::$ADJECTIVE, '')) {
                    self::replace($RV, self::$PARTICIPLE, '');
                } else {
                    if (!self::replace($RV, self::$VERB, ''))
                        self::replace($RV, self::$NOUN, '');
                }
            }

            # Step 2
            self::replace($RV, '/и$/iu', '');

            # Step 3
            if (self::match($RV, self::$DERIVATIONAL))
                self::replace($RV, '/ость?$/iu', '');

            # Step 4
            if (!self::replace($RV, '/ь$/iu', '')) {
                self::replace($RV, '/ейше?/iu', '');
                self::replace($RV, '/нн$/iu', 'н');
            }

            $stem = $start.$RV;
        } while(false);

        return $stem;
    }
}