<?php

namespace App\Http\Controllers;

use App\Model\Draft;
use Illuminate\Http\Request;

class DraftController extends Controller
{
    /**
     * 获取草稿
    */
    public function index(Request $request)
    {
        $authUser = $this->authUser();
        $where = [
            ['user_id', '=', $authUser->id],
            ['type', '=', $request->input('type','car')]
        ];
        $mark_id = $request->input('mark_id');
        if (!empty($mark_id)) {
            $where[] = ['mark_id', '=', $mark_id];
        }

        $data = Draft::where($where)->orderBy('created_at', 'desc')->first();
        return $this->responseSuccess($data);
    }

    /**
     * 保存草稿
    */
    public function store(Request $request)
    {
        $authUser = $this->authUser();
        $type = $request->input('type','car');
        $mark_id = $request->input('mark_id');
        $content = $request->input('content');
        try {
            $where = [
                ['user_id', '=', $authUser->id],
                ['type', '=', $type]
            ];
            if (!empty($mark_id)) {
                $where[] = ['mark_id', '=', $mark_id];
            }
            $mDraft = Draft::where($where)->orderBy('created_at', 'desc')->first();
            if (empty($mDraft)) {
                $mDraft = new Draft();
                $mDraft->user_id = $authUser->id;
                $mDraft->type = $type;
                $mDraft->content = $content;
                $mDraft->mark_id = $mark_id;
            } else {
                $mDraft->content = array_merge($mDraft->content, $content);
            }
            $mDraft->save();

        } catch (\Exception $e) {
            return $this->responseError('err:' . $e->getMessage());
        }
        return $this->responseSuccess(['draft_id' => $mDraft->id]);
    }
}
