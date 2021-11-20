<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    use ApiResponse;

    // 文件上传
    public function upload(Request $request)
    {
        // 文件存在就覆盖，不存在就覆盖并新增一条记录
//        $user = Auth::user();
        $user = ['id' => 1];
        $file = $request->file('file');

        if ($file->isValid()) {
            try {
                DB::beginTransaction();
                $save_path = "file/{$user['id']}";

                $is_exists = Storage::disk('public')->exists($save_path.$file->getClientOriginalName());
                if (!$is_exists) {
                    $fileModel = File::query()->create([
                        'user_id' => $user['id'],
                        'file_name' => $file->getClientOriginalName(),
                    ]);
                }
                $file_path = Storage::disk('public')->putFileAs($save_path, $file, $file->getClientOriginalName());

                if (!$file_path) {
                    throw new \Exception('文件保存到服务器失败');
                }

                if (!$is_exists) {
                    $fileModel->file_path = $file_path;
                    $fileModel->save();
                }

                DB::commit();
                return $this->success([], '上传成功');
            } catch (\Exception $exception) {
                DB::rollBack();

                return $this->error([], '上传失败'.$exception->getMessage());
            }
        }

        return $this->error([], '无效的文件对象，请重新上传');
    }

    // 文件展示
    public function index(Request $request)
    {
        $page_size = $request->input('page_size', 15);
        $files = File::query()->where('user_id',1)->orderBy('created_at', 'desc')->paginate($page_size);

        return $this->success($files);
    }

    // 文件删除
    public function destory(Request $request)
    {
        $file_id = $request->input('file_id');
        $file = File::query()->find($file_id, ['id', 'file_name', 'file_path']);
        if (!$file) {
            return $this->error([], '文件未找到');
        }

        try {
            DB::beginTransaction();
            $result = Storage::disk('public')->delete($file['file_path']);
            if (!$result) {
                throw new \Exception('从服务器删除文件失败');
            }
            $file->delete();
            DB::commit();
            return $this->success([], '删除成功');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error([], '删除失败'.$exception->getMessage());
        }
    }

    public function checkExists(Request $request)
    {
        $user = ['id' => 1];
        $file = $request->file('file');

        $save_path = "file/{$user['id']}";
        $is_exists = Storage::disk('public')->exists($save_path.$file->getClientOriginalName());

        if ($is_exists) {
            return $this->error([], '文件已存在');
        } else {
            return $this->success([]);
        }

    }
}
