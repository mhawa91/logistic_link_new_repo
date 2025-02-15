<?php

namespace Modules\Admin\Http\Controllers;

use DB;
use Carbon\Carbon;
use Exception;
use Validator;
use Illuminate\Http\Request;
use Modules\SystemCore\Http\Controllers\SystemCoreHttpController;
use Modules\SystemCore\Http\Responses\ActionResponse;
use Modules\Admin\Entities\UserService;
use Modules\Admin\Entities\RoleService;
use App\Models\User;

class UsersDashboardController extends SystemCoreHttpController
{
    public function __construct()
    {
        $this->middleware(config('systemcore.auth.HTTP_AUTH_MIDDLEWARE'));

        $FIRST_MONTH_OF_THE_YEAR = Carbon::parse(date('Y') . '-01-01');
        $this->data['MONTHES_ARRAY'] = collect([
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(1)->subDays(1)->toDateTimeString(),
                'NAME'      => 'JAN',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(1)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(2)->subDays(1)->toDateTimeString(),
                'NAME'      => 'FEB',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(2)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(3)->subDays(1)->toDateTimeString(),
                'NAME'      => 'MAR',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(3)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(4)->subDays(1)->toDateTimeString(),
                'NAME'      => 'APR',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(4)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(5)->subDays(1)->toDateTimeString(),
                'NAME'      => 'MAY',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(5)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(6)->subDays(1)->toDateTimeString(),
                'NAME'      => 'JUN',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(6)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(7)->subDays(1)->toDateTimeString(),
                'NAME'      => 'JUL',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(7)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(8)->subDays(1)->toDateTimeString(),
                'NAME'      => 'AUG',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(8)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(9)->subDays(1)->toDateTimeString(),
                'NAME'      => 'SEP',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(9)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(10)->subDays(1)->toDateTimeString(),
                'NAME'      => 'OCT',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(10)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(11)->subDays(1)->toDateTimeString(),
                'NAME'      => 'NOV',
            ],
            [
                'FROM_DATE' => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(11)->toDateTimeString(),
                'TO_DATE'   => (clone $FIRST_MONTH_OF_THE_YEAR)->addMonths(12)->subDays(1)->toDateTimeString(),
                'NAME'      => 'DEC',
            ],
        ]);
    }

    public function allTypesCount(Request $Request)
    {
        $this->data['DATA'] = [];
        $OPTIONS = [
            'types' => collect($Request->types)
        ];
        $OPTIONS['types']->map(function($item) {
            $this->data['DATA'][ $item['as'] ] = User::whereIn('type', $item['type'])->count();
        });
        return $this->data['DATA'];
    }

    public function countsByMonths(Request $Request, ActionResponse $Response)
    {
        $OPTIONS = [
            'types' => collect($Request->types)
        ];
        $USERS_EACH_MONTH = $OPTIONS['types']->mapWithKeys(function($item, $key) {
            return [
                $item['as'] => $this->data['MONTHES_ARRAY']->map(function($Month) use($item) { 
                    return User::whereIn('type', $item['type'])
                    ->where('created_at', '>=', Carbon::parse($Month['FROM_DATE'])->format('Y-m-d') . ' 00:00:00')
                    ->where('created_at', '<=', Carbon::parse($Month['TO_DATE'])->format('Y-m-d')   . ' 23:59:59')
                    ->count();
                })
            ];
        });
        return $Response->module('Admin')->code('200')->success()->with([
            'chart'   => [
                'labels'      => $this->data['MONTHES_ARRAY']->map(function($Month) { return $Month['NAME']; })->toArray(),
                'data'        => $USERS_EACH_MONTH->toArray(),
            ],
        ]);
    }

    public function latestAccounts(Request $Request, ActionResponse $Response)
    {
        $OPTIONS = [
            'types'  => $Request->types,
            'limit'  => $Request->limit ?? 15
        ];
        $DATA = User::select([
            '*',
            DB::raw('
                DATE_FORMAT(created_at, "%Y/%m/%d %H:%i") AS new_created_at,
                DATE_FORMAT(updated_at, "%Y/%m/%d %H:%i") AS new_updated_at
            '),
        ])
        ->whereIn('type', $OPTIONS['types'])
        ->orderBy('created_at', 'DESC')
        ->limit($OPTIONS['limit'])
        ->get();
        $DATA->map(function($user) {
            $user->avatar_link = app('GraphManager')->src('400x400', 'user', $user->avatar);
            return $user;
        });
        return $Response->module('Admin')->code('200')->success()->with([
            'users' => $DATA,
        ]);
    }
}