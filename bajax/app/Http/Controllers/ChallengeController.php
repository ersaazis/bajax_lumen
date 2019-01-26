<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Challenge;
use App\ChallengeLog;
use App\WebConfig;
use Auth;

class ChallengeController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:challenge-create', ['only' => ['create','store']]);
         $this->middleware('permission:challenge-edit', ['only' => ['destroyFile','edit','update']]);
         $this->middleware('permission:challenge-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listChallenges = Challenge::orderBy('id','ASC')->get();
        $challenges=array();
        foreach ($listChallenges as $challenge) {
            $challengeLog=ChallengeLog::where(['user_id' => Auth::id(),'challenge_id' => $challenge->id])->count();
            if($challengeLog)
                $challenges[]=array("data"=>$challenge,"finished"=>true);
            else 
                $challenges[]=array("data"=>$challenge,"finished"=>false);
        }
        return response()->json([
            'success' => true,
            'messages' => 'Data Roles !',
            'data' => $challenges,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'point' => 'required|integer',
            'note' => 'required',
            'flag' => 'required|max:191',
            'file1' => 'max:2048',
            'file2' => 'max:2048',
            'file3' => 'max:2048',
            'file4' => 'max:2048',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Please fill in the blank !',
                'data' => $validator->errors(),
            ], 400);
        }

        $data=$request->all();        
        $input=[
            'name' => $data['name'],
            'point' => $data['point'],
            'note' => $data['note'],
            'flag' => $data['flag'],
        ];
        
        if($request->file('file1')){
            $file1=$request->file('file1')->getClientOriginalName();
            $input['file1']=$file1;
        }
        if($request->file('file2')){
            $file2=$request->file('file2')->getClientOriginalName();
            $input['file2']=$file2;
        }
        if($request->file('file3')){
            $file3=$request->file('file3')->getClientOriginalName();
            $input['file3']=$file3;
        }
        if($request->file('file4')){
            $file4=$request->file('file4')->getClientOriginalName();
            $input['file4']=$file4;
        }
        $idChallenge = Challenge::create($input);
        if($idChallenge){
            if($request->file('file1')){
                Storage::disk('challenges')->putFileAs($idChallenge->id, $request->file('file1'), $file1);
            }
            if($request->file('file2')){
                Storage::disk('challenges')->putFileAs($idChallenge->id, $request->file('file2'), $file2);
            }
            if($request->file('file3')){
                Storage::disk('challenges')->putFileAs($idChallenge->id, $request->file('file3'), $file3);
            }
            if($request->file('file4')){
                Storage::disk('challenges')->putFileAs($idChallenge->id, $request->file('file4'), $file4);
            }

            return response()->json([
                'success' => true,
                'messages' => 'Add Challenge Success !',
                'data' => $idChallenge
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Insert Challenge!',
                'data'=>NULL,
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $challenge = Challenge::find($id);
        $challengeLog=ChallengeLog::where(['user_id' => Auth::id(),'challenge_id' => $id])->count();
        $urlFile=array();
        $urlFile[1]=Storage::disk('challenges')->url($id.'/'.$challenge->file1);
        $urlFile[2]=Storage::disk('challenges')->url($id.'/'.$challenge->file2);
        $urlFile[3]=Storage::disk('challenges')->url($id.'/'.$challenge->file3);
        $urlFile[4]=Storage::disk('challenges')->url($id.'/'.$challenge->file4);

        if($challenge)
            return response()->json([
                'success' => true,
                'messages' => 'Show Challenge!',
                'data' => [
                    'challenge' => $challenge,
                    'urlFile' => $urlFile
                    'challengeLog' => $challengeLog
                ]
            ], 200);
        else
            return response()->json([
                'success' => false,
                'messages' => 'No Challenge!',
                'data' => NULL
            ], 400);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $challenge = Challenge::find($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'point' => 'required|integer',
            'note' => 'required',
            'flag' => 'required|max:191',
            'file1' => 'max:2048',
            'file2' => 'max:2048',
            'file3' => 'max:2048',
            'file4' => 'max:2048',
        ]);
        if ($validator->fails() || !$challenge){
            return response()->json([
                'success' => false,
                'messages' => empty($validator->fails())?'Challenge Not Found !':'Please fill in the blank !',
                'data' => empty($validator->fails())?NULL:$validator->errors(),
            ], 400);
        }

        $data=$request->all();        
        $input=[
            'name' => $data['name'],
            'point' => $data['point'],
            'note' => $data['note'],
            'flag' => $data['flag'],
        ];
        
        if($request->file('file1')){
            $file1=$request->file('file1')->getClientOriginalName();
            $input['file1']=$file1;
        }
        if($request->file('file2')){
            $file2=$request->file('file2')->getClientOriginalName();
            $input['file2']=$file2;
        }
        if($request->file('file3')){
            $file3=$request->file('file3')->getClientOriginalName();
            $input['file3']=$file3;
        }
        if($request->file('file4')){
            $file4=$request->file('file4')->getClientOriginalName();
            $input['file4']=$file4;
        }
        WebConfig::findByName('update_point')->update(['value'=>'1']);
        $idChallenge = Challenge::find($id)->update($input);
        if($idChallenge){
            if($request->file('file1')){
                Storage::disk('challenges')->putFileAs($id, $request->file('file1'), $file1);
            }
            if($request->file('file2')){
                Storage::disk('challenges')->putFileAs($id, $request->file('file2'), $file2);
            }
            if($request->file('file3')){
                Storage::disk('challenges')->putFileAs($id, $request->file('file3'), $file3);
            }
            if($request->file('file4')){
                Storage::disk('challenges')->putFileAs($id, $request->file('file4'), $file4);
            }

            return response()->json([
                'success' => true,
                'messages' => 'Update Challenge Success !',
                'data' => $idChallenge
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Update Challenge!',
                'data'=>NULL,
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $challenge = Challenge::find($id)->delete();
        Storage::disk('challenges')->deleteDirectory($id);
        return response()->json([
            'success' => true,
            'messages' => 'Delete Challenge Success !',
            'data'=>NULL,
        ], 200);
    }
    public function destroyFile($id,$file)
    {
        $challenge = Challenge::find($id);        
        if ($challenge) {
            if($challenge->{$file}){
                Storage::disk('challenges')->delete($id.'/'.$challenge->{$file});
                $challenge->update([$file => ""]);
                return response()->json([
                    'success' => true,
                    'messages' => 'Delete File Challenge Success !',
                    'data'=>NULL,
                ], 200);
            }
            else
                return response()->json([
                    'success' => false,
                    'messages' => 'File Challenge Not Found !',
                    'data'=>NULL,
                ], 400);
        }
        else
            return response()->json([
                'success' => false,
                'messages' => 'Challenge Not Found !',
                'data'=>NULL,
            ], 400);

    }
}
