<?php
/**
 * Controller generated using LaraAdmin
 * Help: http://laraadmin.com
 * LaraAdmin is open-sourced software licensed under the MIT license.
 * Developed by: Dwij IT Solutions
 * Developer Website: http://dwijitsolutions.com
 */

namespace App\Http\Controllers\LA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use Auth;
use DB;
use Validator;
use Datatables;
use Collective\Html\FormFacade as Form;
use Dwij\Laraadmin\Models\Module;
use Dwij\Laraadmin\Models\ModuleFields;

use App\Models\Upload;

class UploadsController extends Controller
{
    public $show_action = true;
    
    /**
     * Display a listing of the Uploads.
     *
     * @return mixed
     */
    public function index()
    {
        $module = Module::get('Uploads');
        
        if(Module::hasAccess($module->id)) {
            return View('la.uploads.index', [
                'show_actions' => $this->show_action,
                'listing_cols' => Module::getListingColumns('Uploads'),
                'module' => $module
            ]);
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }
    
    /**
     * Show the form for creating a new upload.
     *
     * @return mixed
     */
    public function create()
    {
        //
    }
    
    /**
     * Store a newly created upload in database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if(Module::hasAccess("Uploads", "create")) {
            
            $rules = Module::validateRules("Uploads", $request);
            
            $validator = Validator::make($request->all(), $rules);
            
            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            $insert_id = Module::insert("Uploads", $request);
            
            return redirect()->route(config('laraadmin.adminRoute') . '.uploads.index');
            
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }
    
    /**
     * Display the specified upload.
     *
     * @param int $id upload ID
     * @return mixed
     */
    public function show($id)
    {
        if(Module::hasAccess("Uploads", "view")) {
            
            $upload = Upload::find($id);
            if(isset($upload->id)) {
                $module = Module::get('Uploads');
                $module->row = $upload;
                
                return view('la.uploads.show', [
                    'module' => $module,
                    'view_col' => $module->view_col,
                    'no_header' => true,
                    'no_padding' => "no-padding"
                ])->with('upload', $upload);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("upload"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }
    
    /**
     * Show the form for editing the specified upload.
     *
     * @param int $id upload ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        if(Module::hasAccess("Uploads", "edit")) {
            $upload = Upload::find($id);
            if(isset($upload->id)) {
                $module = Module::get('Uploads');
                
                $module->row = $upload;
                
                return view('la.uploads.edit', [
                    'module' => $module,
                    'view_col' => $module->view_col,
                ])->with('upload', $upload);
            } else {
                return view('errors.404', [
                    'record_id' => $id,
                    'record_name' => ucfirst("upload"),
                ]);
            }
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }
    
    /**
     * Update the specified upload in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id upload ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if(Module::hasAccess("Uploads", "edit")) {
            
            $rules = Module::validateRules("Uploads", $request, true);
            
            $validator = Validator::make($request->all(), $rules);
            
            if($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();;
            }
            
            $insert_id = Module::updateRow("Uploads", $request, $id);
            
            return redirect()->route(config('laraadmin.adminRoute') . '.uploads.index');
            
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }
    
    /**
     * Remove the specified upload from storage.
     *
     * @param int $id upload ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        if(Module::hasAccess("Uploads", "delete")) {
            Upload::find($id)->delete();
            
            // Redirecting to index() method
            return redirect()->route(config('laraadmin.adminRoute') . '.uploads.index');
        } else {
            return redirect(config('laraadmin.adminRoute') . "/");
        }
    }
    
    /**
     * Server side Datatable fetch via Ajax
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function dtajax(Request $request)
    {
        $module = Module::get('Uploads');
        $listing_cols = Module::getListingColumns('Uploads');
        
        $values = DB::table('uploads')->select($listing_cols)->whereNull('deleted_at');
        $out = Datatables::of($values)->make();
        $data = $out->getData();
        
        $fields_popup = ModuleFields::getModuleFields('Uploads');
        
        for($i = 0; $i < count($data->data); $i++) {
            for($j = 0; $j < count($listing_cols); $j++) {
                $col = $listing_cols[$j];
                if($fields_popup[$col] != null && starts_with($fields_popup[$col]->popup_vals, "@")) {
                    $data->data[$i][$j] = ModuleFields::getFieldValue($fields_popup[$col], $data->data[$i][$j]);
                }
                if($col == $module->view_col) {
                    $data->data[$i][$j] = '<a href="' . url(config('laraadmin.adminRoute') . '/uploads/' . $data->data[$i][0]) . '">' . $data->data[$i][$j] . '</a>';
                }
                // else if($col == "author") {
                //    $data->data[$i][$j];
                // }
            }
            
            if($this->show_action) {
                $output = '';
                $output .= '<div class="btn-group">';
                if (Module::hasAccess("Uploads", "edit")) {
                    $output .= '<a href="'.url(config('laraadmin.adminRoute') . '/uploads/'.$data->data[$i][0].'/edit').'" class="btn btn-warning btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-edit"></i></a>';
                }
                if (Module::hasAccess("Uploads", "delete")) {
                    $output .= '<a href="#" data-toggle="modal" data-target="#DeleteModal'.$data->data[$i][0].'"  class="btn btn-danger btn-xs" style="display:inline;padding:2px 5px 3px 5px;"><i class="fa fa-times"></i></a>';
                }
                $output .= '</div>';
                if (Module::hasAccess("Uploads", "delete")) {
                    $output .= '<div class="modal fade" id="DeleteModal'.$data->data[$i][0].'" role="dialog" aria-labelledby="myModalLabel">';
                    $output .= '    <div class="modal-dialog" role="document">';
                    $output .= '         <div class="modal-content">';
                    $output .= '            <div class="modal-header">';
                    $output .= '                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
                    $output .= '                <h4 class="modal-title" id="myModalLabel">Uploads delete confirmation</h4>';
                    $output .= '            </div>';
                    $output .= '			<div class="modal-body"> Are you sure you want to delete this entry? </div>';
                    $output .= '           <div class="modal-footer">';
                    $output .= '                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">No</button>';
                    $output .=                  Form::open(['route' => [config('laraadmin.adminRoute') . '.uploads.destroy',$data->data[$i][0]], 'method' => 'delete', 'style'=>'display:inline']);
                    $output .= '                    <button class="btn btn-danger pull-right" type="submit">Yes</button>';
                    $output .=                  Form::close();
                    $output .= '            </div>';
                    $output .= '       </div>';
                    $output .= '   </div>';
                    $output .= '</div>';
                }
                $data->data[$i][] = (string)$output;
            }
        }
        $out->setData($data);
        return $out;
    }
}
