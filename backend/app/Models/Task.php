<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  // Logic: Only allow these columns to be filled by the user for security
  protected $fillable = ['title', 'description', 'status'];
}
