#!/bin/sh
./run_once.sh weekly S5ReadLecturerSchedules
./run_once.sh weekly S6ReadClassSchedules
./run_once.sh weekly S7FillSearchTables
./run_once.sh weekly S8FillStatsTables
