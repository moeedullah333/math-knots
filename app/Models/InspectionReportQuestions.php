<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionReportQuestions extends Model
{
    use HasFactory;

    protected $table = "inspection_report_questions";

    public function category_detail()
    {
        return $this->hasOne(Categories::class, 'id', 'category_id')->select(['id', 'name']);
    }

    public function sub_category_detail()
    {
        return $this->hasOne(SubCategories::class, 'id', 'sub_category_id')->select(['id', 'name']);
    }
}
