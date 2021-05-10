<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\StpUser\ShowDatas;
use App\Model\StpUser;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class StpUserController extends AdminController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'StpUser';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new StpUser());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
//        $grid->column('email_verified_at', __('Email verified at'));
//        $grid->column('password', __('Password'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->column('gender', __('Gender'))->using(StpUser::$genders);
        $grid->column('birthday', __('Birthday'));
        $grid->column('ethnic_bg', __('Ethnic bg'));
        $grid->column('height', __('Height/cm'));
        $grid->column('weight', __('Weight/kg'));
        $grid->column('blood_type', __('Blood type'));


        $grid->disableCreateButton();

        $grid->actions(function ($actions) {
            // 去掉删除
            $actions->disableDelete();

            // 去掉编辑
            $actions->disableEdit();

            // 去掉查看
            $actions->disableView();

            $actions->add(new ShowDatas());
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(StpUser::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('password', __('Password'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
        $show->field('gender', __('Gender'));
        $show->field('birthday', __('Birthday'));
        $show->field('ethnic_bg', __('Ethnic bg'));
        $show->field('height', __('Height'));
        $show->field('weight', __('Weight'));
        $show->field('blood_type', __('Blood type'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new StpUser());

        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
        $form->password('password', __('Password'));
        $form->text('gender', __('Gender'))->default('1');
        $form->text('birthday', __('Birthday'));
        $form->text('ethnic_bg', __('Ethnic bg'));
        $form->number('height', __('Height'));
        $form->number('weight', __('Weight'));
        $form->text('blood_type', __('Blood type'));

        return $form;
    }



}
