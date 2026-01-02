<?php
Route::get('cities/first-letters/{county}', [CityController::class, 'getFirstLetters']);
Route::get('cities/by-letter/{county}/{letter}', [CityController::class, 'getCitiesByLetter']);