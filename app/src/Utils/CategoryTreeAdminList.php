<?php

namespace App\Utils;

use App\Twig\Extension\AppExtension;
use App\Utils\AbstractClasses\CategoryTreeAbstract;

class CategoryTreeAdminList extends CategoryTreeAbstract
{


    // public function getcategorylistAndParent(int $id): string
    // {
    //     $this->slugger = new AppExtension;
    //     $parentData = $this->getMainParent($id);
    //     $this->mainParentName = $parentData['name'];
    //     $this->mainParentId = $parentData['id'];
    //     $key = array_search($id, array_column($this->categoriesArrayFromDb, 'id'));
    //     $this->currentCategoryName = $this->categoriesArrayFromDb[$key]['name'];

    //     $categories_array = $this->buildTree($parentData['id']);
    //     dump($this);
    //     return $this->getcategorylist($categories_array);
    // }

    public function getcategorylist(array $categories_array)
    {
        $this->categorylist .= '<ul class="fa-ul text-left">';
        foreach ($categories_array as $value) {
            $url_edit = $this->urlgenerator->generate('edit_category', ['id' => $value['id']]);
            $url_delete = $this->urlgenerator->generate('delete_category', ['id' => $value['id']]);
            $this->categorylist .= " <li><i class='fa-li fa fa-arrow-right'></i>{$value['name']}  <a href='{$url_edit}'>edit</a> <a href='{$url_delete}'>delete</a>";
            if (!empty($value['children'])) {
                $this->getcategorylist($value['children']);
            }
            $this->categorylist .= '</li>';
        }
        $this->categorylist .= '</ul>';

        return $this->categorylist;
    }
}
