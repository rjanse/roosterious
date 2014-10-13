<?php
class S7FillSearchTables implements iSubscript {
  public function execute($oMysqli) {
    //Remove all entries
    $oMysqli->query("DELETE FROM search_lecturer;");
    $oMysqli->query("DELETE FROM search_class;");
    $oMysqli->query("DELETE FROM search_room;");

    
    
    //Fill lecturer_search table
    $oMysqli->query("INSERT INTO search_lecturer(searchwords, lecturer_id, activities) SELECT CONCAT(lecturer.id, \" \", lecturer.name), lecturer.id, GROUP_CONCAT(DISTINCT lesson.activity_id) FROM lecturer, lesson, lessonlecturers WHERE lecturer.id = lessonlecturers.lecturer_id AND lesson.id = lessonlecturers.lesson_id GROUP BY lecturer_id;");
    
    //Fill class search table
    $oMysqli->query("INSERT INTO search_class(searchwords, class_id, activities) SELECT class.id, class.id, GROUP_CONCAT(DISTINCT lesson.activity_id) FROM class, lesson, lessonclasses WHERE class.id = lessonclasses.class_id AND lesson.id = lessonclasses.lesson_id GROUP BY class_id");
    
    //Fill room search table
    $oMysqli->query("INSERT INTO search_room(searchwords, room_id, activities) SELECT room.id, room.id, GROUP_CONCAT(DISTINCT lesson.activity_id) FROM room, lesson, lessonrooms WHERE room.id = lessonrooms.room_id AND lesson.id = lessonrooms.lesson_id GROUP BY room_id;");
    
  }  
}
?>
