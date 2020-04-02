<?php



include "vk_api.php"; 



require  './vendor/autoload.php'; // вызов библиотеки базы данных
use Krugozor\Database\Mysql\Mysql as Mysql; // вызов mysql
$db = Mysql::create("localhost", "id11009594_bot", "Nikita2004")->setDatabaseName("id11009594_bot")->setCharset("utf8"); // подключение к библиотеке



const VK_KEY = "767e71d3834c2e7e8d3edb4e552da06f4677934a50616df56fd26dbc98613a1e251dfa7e8d8fc322de305";  // токен сообщества
const ACCESS_KEY = "03a24d35";  // ключ сообщества
const VERSION = "5.92"; // версия API вконтакте



$vk = new vk_api(VK_KEY, VERSION); // создание экземпляра класса работы с api, принимает токен и версию api
$data = json_decode(file_get_contents('php://input')); //получаем и декодируем JSON пришедший из ВК



if ($data->type == 'confirmation') { // если vk запрашивает ключ
    exit(ACCESS_KEY); // завершаем скрипт отправкой ключа
}
echo 'ok'; // говорим vk, что мы приняли callback



// ====== Наши переменные ============
$id = $data->object->from_id; //  ID пользователя
$peer_id = $data->object->peer_id; //  ID беседы
$message = $data->object->text; // Само сообщение от пользователя
$date_today = date("d.m.y"); // получение даты в виде день месяц год
$min = date("H") + 3 ; //  получение часов(т.к. время на хосте отстает н атри часа)
$minn = date("i"); // получение минут
$reply_author = $data->object->reply_message->from_id; // автор пересланного сообщения
$is_admin = [434529940,365688934,396756747]; //массив с ID админов
$chat_id = $peer_id - 2000000000; // чат ID
$is_admien=[434529940]; // массив созданный для главного админа(может назначать админов в базу данных)
// ====== *************** ============
        








if ($data->type == 'message_new') { // Если это новое сообщение то выполняем код указанный в условии
 
 
  






///////////////////////////////////////// сложные команды /////////////////////////////////////////

  if (mb_substr($message,0,5) == 'пх'){

if ($reply_author == ''){
    
}else{
  $vk->sendMessage($peer_id, $reply_author);

}



/////////////////////////////////////////
// относительная очистка беседы путем спама 10 сообщений
   if (mb_substr($message,0,5) == '/спам'){ // Обрезаем сообщение и сравниваем что получилось

            if (in_array($id, $sql)) { // проверка ID пользователя с ID указанным в базе данных
 
      
             $vk->sendMessage($peer_id, "спам начат");
           $vk->sendMessage($peer_id, "нота");
            $vk->sendMessage($peer_id, "бот");
           $vk->sendMessage($peer_id, "нота");
           $vk->sendMessage($peer_id, "бот");
            $vk->sendMessage($peer_id, "нота");
            $vk->sendMessage($peer_id, "бот");
           $vk->sendMessage($peer_id, "нота");
            $vk->sendMessage($peer_id, "бот");
            $vk->sendMessage($peer_id, "спам закончен");

     
    } else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");

       }
   }
/////////////////////////////////////////









/////////////////////////////////////////
// запись администратора в базу данных (/админ @ID_пользователя) 
 if (mb_substr($message,0,6) == '/админ'){ 

        if (in_array($id, $is_admien)) { // проверка ID пользователя с ID указанным в массиве для ГА

            $user_id = mb_substr($message ,7); // еще раз обрезаем и получаем все что написано после !админ_
            $user_id = explode("|", mb_substr($user_id, 3))[0];

            if($user_id == ""){
                $vk->sendMessage($peer_id, "Вы забыли указать аргумент");

            } else {

          $sql = $db->query("SELECT * from admin WHERE vk_id = $user_id")->getNumRows(); // Проверяем на наличие записи в БД
             if ($sql){ // Если есть запись, сообщим об этом

               $vk->sendMessage($peer_id, "Этот пользователь уже сохранен в базе данных.");
             } else { // Иначе создаем новую запись
                 $db->query("INSERT INTO admin (vk_id) VALUES ($user_id)");

                 $vk->sendMessage($peer_id, "id - {$user_id}");
                }


            }
        } else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");

        }
    }
/////////////////////////////////////////









/////////////////////////////////////////
// просмотр админ списка  
if (mb_substr($message,0,8) == '/адмлист'){ // Обрезаем сообщение и сравниваем что получилось

        $is_admins = $db->query("SELECT * FROM admin")->fetch_assoc()['vk_id']; // Получаем данные из колонки vk_id

        $vk->sendMessage($peer_id, "Данные с БД - $is_admins");


        $is_admins = $db->query("SELECT * FROM admin"); // Получаем данные из колонки vk_id
        while ($row = $is_admins->fetch_assoc()) { // Запускаем цикл

            $is_adminss .= $row['vk_id']. " - запись с бд\n";

        }
        $vk->sendMessage($peer_id, "ЦИКЛ\n $is_adminss");

    }
/////////////////////////////////////////









/////////////////////////////////////////
// обновление базы данных
    if (mb_substr($message,0,9) == '/обновить'){ // Обрезаем сообщение и сравниваем что получилось

        $db->query("UPDATE admin SET vk_id = 777777 WHERE vk_id = 87444494  LIMIT 1"); // WHERE - Поиск записей в которые нужно внести изменения, перечесляются через AND, LIMIT 1 - Найти 1 запись, другие похожие не трогать


        $vk->sendMessage($peer_id, "Данные обновлены");

    }
/////////////////////////////////////////









/////////////////////////////////////////
// команда создана для того чтобы администратор мог быстро введя /цифры @ID_пользователя узнать ID цифрами
 if (mb_substr($message,0,6) == '/цифры'){ // Обрезаем сообщение и сравниваем что получилось

        if (in_array($id, $is_admin)) { // С помощью in_array проверяем схожесть переменной $id с массивом с ID's

            $user_id = mb_substr($message ,7); // еще раз обрезаем и получаем все что написано после !админ_
            $user_id = explode("|", mb_substr($user_id, 3))[0];

            if($user_id == ""){
                $vk->sendMessage($peer_id, "Вы забыли указать аргумент");

            } else {

               
                $vk->sendMessage($peer_id, "id - {$user_id}");

            }
        } else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");
}
        
    }
/////////////////////////////////////////









/////////////////////////////////////////
// команда создана для того чтобы уведомлять всех пользователей беседы(/обвл тест)
if (mb_substr($message,0,5) == '/обвл'){
        
  // $get_members = $vk->request('messages.getConversationMembers', ['peer_id' => $peer_id]); // Получили список пользователей беседы
   // foreach ($get_members['profiles'] as $member) { // Прошли по массиву для регистрации пользователей по их id
   //   $user_id = $member['id']; // Получили id пользоавтеля
      
            if (in_array($id, $is_admin)) { 
      
            $vk->sendMessage($peer_id, "Объявление - {$kick_id} - @id189374560 (Поля) @id335635632 (Ксения) @id467722323 (Полина) @id401756219 (Александра) @dan1dze @id333153540 (Таисия) @golubicka @lzbrdlnnn @id365688934 (Ульяна) @levsuprun @id491263690 (Зоя) @id491263372 (Арина) @id400322865 (Артём) @id323269958 (Ксюша) @id211340215 (Александр) @id170059816 (Юлия) @nefariushd @soniamish @id222565521 (Вероника) @id482483130 (Лиза) @bobulyaka @id153497583 (Григорий) @ivan_galaev @id546582813 (Екатерина) @polarizasion1 @id397444469 (Даниил) ");
       
    } else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");
        }
    }
/////////////////////////////////////////

  
  
  

    

  
  
/////////////////////////////////////////
// кик пользователя путем указания ID его страницы(/kyck @ID_пользователя), даже если пользователь вышел из беседы
    if (mb_substr($message,0,5) == '/kyck'){

            if (in_array($id, $is_admin)) { 

     $kick_id = mb_substr($message ,6);
        $kick_id = explode("|", mb_substr($kick_id, 3))[0];
        
        if($kick_id == ""){
            $vk->sendMessage($peer_id, "Вы забыли указать аргумент");
        } else {
        $vk->request('messages.removeChatUser', ['chat_id' => $chat_id, 'member_id' => $kick_id]);
    $vk->sendMessage($peer_id, "id - {$kick_id} был исключен :-)");
    }
    } else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");
        }
    } 
/////////////////////////////////////////
    
   
  
     
    
   
  


/////////////////////////////////////////
// кик пользователя путем ответа на его сообщение(администратор ОТВЕЧАЕТ на сообщение пользователя(/kick))
if (mb_substr($message,0,5) == '/kick'){
        if (in_array($id, $is_admin)) { 
    $userInfo = $vk->request("users.get", ["user_ids" => $id]);
$first_name = $userInfo[0]['first_name'];
    
    
    if($reply_author == ''){
      $vk->sendMessage($peer_id, "Вы забыли указать аргумент (перешлите любое его сообщение)");
    }else{
      $userInfo_reply = $vk->request("users.get", ["user_ids" => $reply_author]);
      $first_name_reply = $userInfo[0]['first_name'];
      $vk->sendMessage($peer_id, "прощайте");
      $vk->request('messages.removeChatUser', ['chat_id' => $chat_id, 'member_id' => $reply_author]);
  
    }
    } else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");
    }
  }
/////////////////////////////////////////









/////////////////////////////////////////
// проверка работоспособности бота, сделано не сколько для администрации сколько для кодера
if (mb_substr($message,0,5) == 'тут?'){
        if (in_array($id, $is_admin)) { 

      
    $vk->sendMessage($peer_id, "да мой господин.");
        }
}
/////////////////////////////////////////


   






/////////////////////////////////////////
// просто функция для администрации когда лень печатать
   if (mb_substr($message,0,5) == '/смех'){

            if (in_array($id, $is_admin)) {                   
            $vk->sendMessage($peer_id, "АХАХАХАХАХХАХАХАХАХАХАХАХХАХАХАХААААААААХХХХХХААХХАХАХА");
    } else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");
        }
    }
/////////////////////////////////////////









/////////////////////////////////////////
// команда для администрации для проверки пользователей находящихся онлайн
   if(in_array($message, ['онлайн', 'online'])){
            if (in_array($id, $is_admin)) { 
    if($chat_id > 0){ // Если это беседа
      $members = $vk->request('messages.getConversationMembers', ['peer_id' => $peer_id]); // Запрос на получение данных о пользователях беседы
      foreach ($members['profiles'] as $useronline) { // При помощи foreach производим работу над данными из пришедшего нам массива
        if ($useronline['online'] == 1) { // Если проверяемый пользователь в сети
          $userOnline++; // Добавляем 1 к общему числу онлайна

          $userInfoOnline = $vk->request("users.get", ["user_ids" => $useronline['id'], "fields" => "last_seen"]); // Запрос данных пользователя
          $first_nameOnline = $userInfoOnline[0]['first_name']; // Имя
          $last_nameOnline = $userInfoOnline[0]['last_name']; // Фамилия
          $platformOnline = $userInfoOnline[0]['last_seen']['platform']; // Платформа
          if ($platformOnline >= 1 && $platformOnline <= 5) { // 1 - 5 отнесем к телефонам
            $platformOnline = '📱';
          }else{ // остальные ПК
            $platformOnline = '💻';
          }
          $Onlinelist .= "🗣 @id{$useronline['id']} ({$first_nameOnline} {$last_nameOnline})"."   - ".$platformOnline."\n"; // Составили текст с онлайн людьми
        }
      }
      $vk->sendMessage($peer_id, "
      📍 Сейчас в сети: {$userOnline} 📍:
      {$Onlinelist}
      ");
    }else{ // Если это лс с ботом
      $vk->sendMessage($peer_id, "Команда 'Онлайн' доступна только в беседах");
    }
    }else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");
        }
        
    }
/////////////////////////////////////////
   
  


  




///////////////////////////////////////// простые команды /////////////////////////////////////////









/////////////////////////////////////////
// состав администрации
  if ($message == '!адмсостав') {

           $vk->sendMessage($peer_id, "
           Готов предсватить моих самых любимых живых существ, из мира людей, это:
           @id434529940(Дон - @id434529940), @id365688934(Креветка - @id365688934),
           @id396756747 (Эль Капо - @id396756747)");
            
        }
/////////////////////////////////////////









/////////////////////////////////////////
// отправление смешной картинки из альбома в вк
if($message == '!мем'){
$img = ['434529940_457258631','434529940_457258630','434529940_457258629','434529940_457258628','434529940_457258627','434529940_457258626','434529940_457258622','434529940_457258621','434529940_457258620','434529940_457258619','434529940_457258618','434529940_457258617','434529940_457258616','434529940_457258615','434529940_457258614','434529940_457258613','434529940_457258602','434529940_457258601','434529940_457258600','434529940_457258599','434529940_457258598','434529940_457258597','434529940_457258596','434529940_457258595','434529940_457258594','434529940_457258593','434529940_457257086'];
$rand_img = array_rand($img, 1);
$vk->request('messages.send', ['peer_id' => $peer_id, 'attachment' => "photo{$img[$rand_img]}"]);
    }
/////////////////////////////////////////









/////////////////////////////////////////
// команда для администрации чтобы узнать возможности
 if ($message == '!адмкоманды'){ 
           if (in_array($id, $is_admin)) { 
            $vk->sendMessage($peer_id, "
            Команды для админов!
            /kick - кик пользователя, пересылая сообщение пользователя,
            /kyck - кик пользователя, указывая айди пользователя,
            /смех - бот смеётся,
            /объявление - рассылка уведомлений для всех людей в беседе, 
            /спам - бот несколько раз спамит дабы немножко очистить беседу* 
            /цифры - бот отправляет вам айди пользователя в цифрах
            тут?,  Бот - проверка работаспособности бота.
            онлайн - проверка и упоминание пользователей в сети
            * - функция в заморозке");
            
           } else {
            $vk->sendMessage($peer_id, "У Вас нет доступа к этой команде!");
        }
           }
/////////////////////////////////////////









/////////////////////////////////////////
// бот отправляет несколько албомов с музыкой из вк
if ($message == '!музыка') {
            $vk->sendMessage($peer_id, " 
Мировые топ-хиты
https://vk.com/music/album/-115799210_2


Радио-хиты России
https://vk.com/music/album/-115799210_1 


");
        }
/////////////////////////////////////////









/////////////////////////////////////////
// вывод команд бота
  if ($message == '!команды') {

           $vk->sendMessage($peer_id, "Привет! Я бот Нотов, проще - Нотабот. Вот всё что я умею
1. !расписание - отправляю тебе расписание на целую неделю.

2. !инстаграм - отправляю тебе логин и пароль от нашего, классного инстаграма.

3. !предметы - отправляю тебе список уроков, где они проводятся и кто собственно их проводит.

4. !время - отпрявляю тебе время начала и конца уроков.  

5. !классная - отправляю тебе контакты классного руководителя.   

6. !адрес - отпрвляю тебе адрес нашей школы.     

7. !дата - отправлю тебе дату.

8. !музыка - отправляю тебе плейлист с музыкой.

9. !отдых - без комментариев.

10. !сегодня - отправляю тебе расписание на сегодня.

11. !завтра - отправляю тебе расписание на завтра.

12. !адмсостав - оптравлю тебе список админов.

13. !мем - отправляю тебе смешную картинку.
Вот и всё! Конечно ты можешь написать в мне в личку, может научусь у тебя чему-нибудь! Удачи!!!"); // Отправляем ответ
            
        }
/////////////////////////////////////////






    
        
  
/////////////////////////////////////////
// вывод времени окончания/начала уроков
if ($message == '!время') {

            $vk->sendMessage($peer_id, "Первый урок(9:00-9:45)
Второй урок(9:55-10:40)
Третий урок(11:00-11:45)
Четвертый урок(12:05-12:50)
Пятый урок(13:00-13:45)
Шестой урок(13:55-14:00)
Седьмой урок(14:45-15:35)");
            
        }
///////////////////////////////////////// 
  
	

		





/////////////////////////////////////////
// отправка данных от инстаграм аккаунта
if ($message == '!инстаграм') {

            $vk->sendMessage($peer_id, "Логин - 9l_official_page_
Пароль - shkola43");
            
        }
/////////////////////////////////////////









/////////////////////////////////////////
// отправка данных от инстаграм аккаунта
if ($message == 'ДО') {

            $vk->sendMessage($peer_id, "
    9 Л класс. Зайцева Людмила Александровна

    Русский язык и литература — Зайцева Людмила Александровна
    https://cloud.mail.ru/public/4GN7/3VoxiZWTT/
    
    Алгебра и геометрия - Сильвестрова Надежда Николаевна
    https://cloud.mail.ru/public/2bXK/4vsjuyJnA/
    
    Информатика -
        Шимова Екатерина Алексеевна, 
    https://cloud.mail.ru/public/M4R6/miwUac9NH/
        Потемина Надежда Анатольевна
    https://cloud.mail.ru/public/GhCy/SYNM8PDBx/
        
    История  и обществознание - Гундерина Инна Аскаровна
    https://yadi.sk/d/HWb-bwCeOO_wow
    
    География - Колиенко Татьяна Владимировна
    https://cloud.mail.ru/public/3J8i/MDSjPC33V/
    
    Биология — Скобникова Ирина Евгеньевна
    https://cloud.mail.ru/public/42XJ/5qMVYqqqL/
    
    Химия — Митрофанова Наталия Петровна
    https://cloud.mail.ru/public/4P7s/2K8UDKr1y/
    
    Физика - Дашевская Вера Александровна
    https://drive.google.com/drive/folders/189iIrbaG9I7azzyePzMzzoF6CtQhsaD1
    
    Английский язык — 
        Федорова Оксана Александровна, 
    https://cloud.mail.ru/public/25tF/5CAmEuPY4/
        Мясоедова Наталия Михайловна
    https://cloud.mail.ru/public/eGdS/3kKtFTxq3/
        
    Испанский язык — 
        Зуева Марина Геннадиевна, 
    https://cloud.mail.ru/public/5LQJ/3i6oeaxei/
        Виноградова Ольга Сергеевн,
    https://cloud.mail.ru/public/5Fi7/3sNhVnWS2/   
        Никульникова Надежда Юрьевна
    https://cloud.mail.ru/public/2BF5/4qQW9R3H5/   
        
    ОБЖ — Токарев Владимир Юрьевич
    https://yadi.sk/d/NR_05XDpOJJiGA
    
    Физкультура - Машкова Вера Юрьевна
    https://cloud.mail.ru/public/aa7a/34YisUeaV/
    
");
            
        }
/////////////////////////////////////////









/////////////////////////////////////////










/////////////////////////////////////////
// бот советует как отдохнуть
 if ($message == '!отдых') {

           $vk->sendMessage($peer_id, "Собственно отдыхать тоже нужно, незабывай что в понедельник нет классной, а во вторник последняя физкультура! 
           ");
            
        }        
/////////////////////////////////////////









/////////////////////////////////////////
// отправка текущего времени
if ($message == '!дата') {

            $vk->sendMessage($peer_id, "Текущее время: $min:$minn и дата: $date_today.");
            
        }
/////////////////////////////////////////









/////////////////////////////////////////
// отправка данных о классном руководителе
if ($message == '!классная') {

            $vk->sendMessage($peer_id, "
Фамилия - Зайцева,
Имя - Людмила,
Отчество - Александровна,
Предметы - Русский язык, литература,
Номер кабинета - 11,
Телефон - 8(965)040-09-93.");
            
        }
/////////////////////////////////////////









/////////////////////////////////////////
// отправка адреса школы
if ($message == '!адрес') {
            $vk->sendMessage($peer_id, "Серебристый бул., 9, корп. 2, Санкт-Петербург");
        }
/////////////////////////////////////////









/////////////////////////////////////////
// отправка кабинетов и привязанных к ним предметов
if ($message == '!предметы') {

            $vk->sendMessage($peer_id, " 
1. Русский язык  (кабинет - 11)
2. Литература  (кабинет - 11)
3. Английский язык  (кабинет - 23,18)
4. Испанский язык  (кабинет - 40б,38,3_)
5. Алгебра  (кабинет - 12)
6. Геометрия  (кабинет - 12)
7. Информатика  (кабинет - 33,31)
8. История  (кабинет - 45)
9. Обществознание  (кабинет - 45)
10. География  (кабинет - 40)
11. Биология  (кабинет - 26)
12. Физика  (кабинет - 10)
13. Химия  (кабинет - 29)
14. Физкультура  (физкультурный зал)
15. ОБЖ  (кабинет - 29)
            	");
        }
/////////////////////////////////////////



	
        
        
   


/////////////////////////////////////////
// отправка расписания
	if ($message == '!расписание') {

           $vk->sendMessage($peer_id, "Понедельник
1. Информатика
2. История
3. Обществознание
4. ОБЖ
5. Английский язык
6. Физика

Вторник
1. Испанский язык
2. Химия
3. Русский язык
4. История
5. Русский язык 
6. Физкультура

Среда
1. Биология
2. Физика
3. Алгебра
4. Физкультура
5. Русский язык 

Четверг
1. Химия
2. Биология
3. Алгебра
4. Физика
5. География
6. Геометрия

Пятница
1. Испанский язык
2. География
3. История
4. Литература 
5. Английский язык
6. Алгебра 
7. Литература

Суббота
1. Алгебра
2. Физкультура 
3. Литература 
4. Испанский язык
5. Испанский язык
6. Геометрия");
            
        }
/////////////////////////////////////////









/////////////////////////////////////////
// вывод уроков на завтра
if ($message == '!завтра') {



function getDay(){ // получения дня недели
    $daaays = array(
     'Воскресенье','Понедельник', 'Вторник', 'Среда',
        'Четверг', 'Пятница', 'Суббота'
    );
    return $daaays[(date('w+1'))];
}




if (date('w') == 0) {
   $vk->sendMessage($peer_id, "
1. Информатика
2. История
3. Обществознание
4. ОБЖ
5. Английский язык
6. Физика");
} 

if (date('w') == 1) {
   $vk->sendMessage($peer_id, "
1. Испанский язык
2. Химия
3. Русский язык
4. История
5. Русский язык 
6. Физкультура");
} 

if (date('w') == 2) {
   $vk->sendMessage($peer_id, "
1. Биология
2. Физика
3. Алгебра
4. Физкультура
5. Русский язык ");
} 

if (date('w') == 3) {
   $vk->sendMessage($peer_id, "
1. Химия
2. Биология
3. Алгебра
4. Физика
5. География
6. Геометрия");
}

if (date('w') == 4) {
   $vk->sendMessage($peer_id, "
1. Испанский язык 
2. География
3. История
4. Литература
5. Английский язык
6. Алгебра 
7. Литература");
} 

if (date('w') == 5) {
   $vk->sendMessage($peer_id, "
1. Алгебра
2. Физкультура
3. Литература
4. Испанский язык
5. Испанский язык
6. Геометрия");
} 
}
/////////////////////////////////////////









/////////////////////////////////////////
if ($message == '!сегодня') {



function getDayRus(){ // получения дня недели
    $days = array(
     'Понедельник', 'Вторник', 'Среда',
        'Четверг', 'Пятница', 'Суббота'
    );
    return $days[(date('w'))];
}



if (date('w') == 1) {
   $vk->sendMessage($peer_id, "
1. Информатика
2. История
3. Обществознание
4. ОБЖ
5. Английский язык
6. Физика");
} 
    
if (date('w') == 2) {
   $vk->sendMessage($peer_id, "
1. Испанский язык
2. Химия
3. Русский язык
4. История
5. Русский язык 
6. Физкультура");
} 

if (date('w') == 3) {
   $vk->sendMessage($peer_id, "
1. Биология
2. Физика
3. Алгебра
4. Физкультура
5. Русский язык ");
} 

if (date('w') == 4) {
   $vk->sendMessage($peer_id, "
1. Химия
2. Биология
3. Алгебра
4. Физика
5. География
6. Геометрия");
}

if (date('w') == 5) {
   $vk->sendMessage($peer_id, "
1. Испанский язык 
2. География
3. История
4. Литература
5. Английский язык
6. Алгебра 
7. Литература");
} 

if (date('w') == 6) {
   $vk->sendMessage($peer_id, "
1. Алгебра
2. Физкультура
3. Литература
4. Испанский язык
5. Испанский язык
6. Геометрия");
} 
}  
/////////////////////////////////////////
        
        
   






///////////////////////////////////////// разговорные команды /////////////////////////////////////////









/////////////////////////////////////////       
if ($message == 'Ок') {
            $vk->sendMessage($peer_id, "в очко тебя чпок
");
}
if ($message == 'ок') {
            $vk->sendMessage($peer_id, "в очко тебя чпок
");
}
/////////////////////////////////////////









/////////////////////////////////////////
if ($message == 'Спасибо') {
            $vk->sendMessage($peer_id, "всегда пожалуйста <3
");
}
if ($message == 'спасибо') {
            $vk->sendMessage($peer_id, "всегда пожалуйста <3
");
}
/////////////////////////////////////////
        








/////////////////////////////////////////
if ($message == 'привет') {
			$vk->sendMessage($peer_id, "салам");
}
if ($message == 'Привет') {
            $vk->sendMessage($peer_id, "салам");
}        
/////////////////////////////////////////









/////////////////////////////////////////
if ($message == 'да') {
            $vk->sendMessage($peer_id, "пизда");     
}
if ($message == 'Да') {
       $vk->sendMessage($peer_id, "пизда");   
}
/////////////////////////////////////////









/////////////////////////////////////////
if ($message == 'Пидор') {
           $vk->sendMessage($peer_id, "забияка");
}
if ($message == 'Пидарас') {
           $vk->sendMessage($peer_id, "забияка");
}
if ($message == 'Пидорас') {
           $vk->sendMessage($peer_id, "забияка");
}
if ($message == 'пидор') {
            $vk->sendMessage($peer_id, "забияка");
}
if ($message == 'пидарас') {
            $vk->sendMessage($peer_id, "забияка");
}
if ($message == 'пидорас') {
           $vk->sendMessage($peer_id, "забияка");
}
/////////////////////////////////////////
        
    
         






/////////////////////////////////////////
if ($message == 'Че') {
           $vk->sendMessage($peer_id, "через плече");
}
if ($message == 'че') {
           $vk->sendMessage($peer_id, "через плече");
}
/////////////////////////////////////////









/////////////////////////////////////////
if ($message == 'Иди на хуй') {
            $vk->sendMessage($peer_id, "твоё место у параши!") ;
}
if ($message == 'Иди нахуй') {
           $vk->sendMessage($peer_id, "твоё место у параши! ");
}
/////////////////////////////////////////









/////////////////////////////////////////
if ($message == 'Пошел нахуй') {
           $vk->sendMessage($peer_id, "тебе туда же");
}
if ($message == 'Пошел на хуй') {
          $vk->sendMessage($peer_id, "тебе туда же");
}
if ($message == 'пошел нахуй') {
           $vk->sendMessage($peer_id, "тебе туда же");
}
if ($message == 'пошел на хуй') {
           $vk->sendMessage($peer_id, "тебе туда же");
}
if ($message == 'иди на хуй') {
           $vk->sendMessage($peer_id, "тебе туда же");
}
if ($message == 'иди нахуй') {
            $vk->sendMessage($peer_id, "тебе туда же");
}
/////////////////////////////////////////



        
        
        
        
        
        
/////////////////////////////////////////
if($message == 'Бот'){
            $vk->sendMessage ($peer_id,"Здарова черти!");
}
/////////////////////////////////////////
        
       
        
        
        
        
  


///////////////////////////////////////// 
if ($message == 'Нет') {
            $vk->sendMessage($peer_id, "минет");
}   
if ($message == 'нет') {
           $vk->sendMessage($peer_id, "минет");
}   
/////////////////////////////////////////









/////////////////////////////////////////
if ($message == 'сука') {
           $vk->sendMessage($peer_id, "че такой злой? Тебя негры донимают?");
}
if ($message == 'Сука') {
           $vk->sendMessage($peer_id, "че такой злой? Тебя негры донимают?");
}
/////////////////////////////////////////        
    
        
        
        
        
   

        

///////////////////////////////////////// не готовое /////////////////////////////////////////        
        
        
        
/////////////////////////////////////////
// вывод погоды со стороннего источника в беседу
//if(in_array($cmd, ['погода', 'погодка', 'weather'])){

//try {



//$city = implode(" ", $args); // Объединили текст после команды в единый
//if($city == ''){ // Проверка на указание города
//$vk->sendMessage($peer_id, "Вы не указали город. Пример: Погода Москва");
//exit; // Завершаем скрипт т.к. не указан город
//}
////$OWApi_key = 'c6c48db8e2970d6002267e6bcba21e1d'; // Ваш ключ от OpenWeatherMap
//$weather=json_decode(file_get_contents("https://api.openweathermap.org/data/2.5/weather?q={$city}&units//=metric&appid={$OWApi_key}&lang=ru")); // Составили запрос к OpenWeatherMap
//if(empty($weather)){ // Если ответ не пришел
//$vk->sendMessage($peer_id, "✖ Ой.. Прости я не поняла, что это за город такой 😿");
//}else{ // Если ответ есть, то составляем текст для вывода
//$list = "В городе " . $weather->name; // Название города
//$list .= "\n🔮 Погода: " . $weather->weather[0]->description; // Название погода (пример: облачно/солнечно)
//$list .= "\n💨 Ветер: " . $weather->wind->speed. " m/s "; // Скорость ветра
//$list .= "\n🌡 Температура: " . $weather->main->temp . "°C"; // Температура
//$list .= "\n☁ Облачность: " . $weather->clouds->all . "%"; // Облачность в процентах
//$list .= "\n📊 Давление: " . $weather->main->pressure . " мм.рт.ст"; // Давление
//$vk->sendMessage($peer_id, $list); // Вывели погоду
//}


//}catch (Exception $e){

//$vk->sendMessage($peer_id, "Ошибка: ".$e->getMessage() ); // Вывели ошибку

//}
//}   
}
