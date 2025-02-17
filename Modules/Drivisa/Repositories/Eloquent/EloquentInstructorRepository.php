<?php

namespace Modules\Drivisa\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Drivisa\Repositories\InstructorRepository;

class EloquentInstructorRepository extends EloquentBaseRepository implements InstructorRepository
{

    /**
     * @inheritDoc
     */
    public function generateNo()
    {
        return Carbon::now()->timestamp;
    }

    /**
     * @inheritDoc
     */
    public function serverPaginationFilteringFor(Request $request)
    {
        $instructors = $this->allWithBuilder();

        if ($request->get('search') !== null) {
            $term = strtolower($request->get('search'));
            $instructors->where('first_name', 'LIKE', "%{$term}%")
                ->orWhere('last_name', 'LIKE', "%{$term}%")
                ->orWhere('no', 'LIKE', "%{$term}%")
                ->orWhere('id', $term);
        }

        $orderBy = $request->get('order_by', 'first_name');
        $order = $request->get('order', 'asc');
        $instructors->orderBy($orderBy, $order);

        if ($request->get('only_verified') === "true") {
            $instructors->where('verified', $request->get('only_verified'));
        }

        // return $instructors->paginate($request->get('per_page', 50));
        return $instructors->get();
    }

    public function searchInstructors($data)
    {
        $instructors = $this->allWithBuilder();

        $result = $instructors
            ->whereHas('user', function ($query) {
                $query->whereNull('blocked_at');
            })
            ->whereHas('stripeAccount')
            ->where('verified', 1)
            ->where('signed_agreement', true);
        $filter = $data['filter'] ?? null;

        $term = strtolower($filter['term'] ?? null);
        $username = strtolower($filter['username'] ?? null);
        $language = $filter['language'] ?? null;
        $make = strtolower($filter['make'] ?? null);
        $address = strtolower($filter['address'] ?? null);
        $date = $filter['date'] ?? null;
        $open_at = $filter['open_at'] ?? null;
        $close_at = $filter['close_at'] ?? null;
        $duration = $filter['duration'] ?? null;

        if ($term || $address) {
            $result = $result
                ->select('drivisa__instructors.*')
                ->distinct('drivisa__instructors.id')
                ->join('drivisa__points as points', 'drivisa__instructors.id', '=', 'points.instructor_id')
                ->where('points.is_active', true)
                ->where(function ($query) use ($address, $term) {
                    $query->when($address, function ($query) use ($address) {
                        $query->where('points.source_name', 'LIKE', "%{$address}%")
                            ->orWhere('points.destination_name', 'LIKE', "%{$address}%")
                            ->orWhere('points.source_address', 'LIKE', "%{$address}%")
                            ->orWhere('points.destination_address', 'LIKE', "%{$address}%");
                    });
                    $query->when($term, function ($query) use ($term) {
                        $query->orWhere(function ($query) use ($term) {
                            $query->orWhere('points.source_name', 'LIKE', "%{$term}%")
                                ->orWhere('points.destination_name', 'LIKE', "%{$term}%")
                                ->orWhere('points.source_address', 'LIKE', "%{$term}%")
                                ->orWhere('points.destination_address', 'LIKE', "%{$term}%")
                                ->orWhere('first_name', 'LIKE', "%{$term}%")
                                ->orWhere('last_name', 'LIKE', "%{$term}%")
                                ->orWhere(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$term}%")
                                ->orWhere(function ($q) use ($term) {
                                    $q->whereHas('user', function ($p) use ($term) {
                                        $p->where('postal_code', 'LIKE', "%{$term}%");
                                        $p->orWhereRaw('LOWER(REPLACE(province, "_", " ")) LIKE ?', ["%{$term}%"]);
                                        $p->orWhere('city', 'LIKE', "%{$term}%");
                                    });
                                });
                        });
                    });
                });
        }

        if ($username) {
            $result->whereHas('user', function ($query) use ($username) {
                $query->where('username', $username);
            });
        }

        if ($language) {
            $result->whereJsonContains('languages', $language);
        }

        if ($make) {
            $result->select('drivisa__instructors.*')
                ->distinct('drivisa__instructors.id')
                ->join('drivisa__cars as cars', 'drivisa__instructors.id', '=', 'cars.instructor_id')
                ->where('make', 'LIKE', "%{$make}%");
        }

        if ($date || $open_at || $close_at || $duration) {
            $result = $result->whereHas('workingDays', function ($query) use ($date, $open_at, $close_at, $duration) {
                $query->whereHas('workingHours', function ($q) use ($open_at, $close_at, $duration) {
                    if ($open_at) {
                        $q->where('open_at', $open_at);
                    }
                    if ($close_at) {
                        $q->where('close_at', $close_at);
                    }
                    if ($duration) {
                        $formatted_duration = sprintf("0%s:00:00", $duration);
                        $q->whereRaw('TIMEDIFF(close_at, open_at) = ?', $formatted_duration)
                            ->where('status', 1);
                    }
                });
                if ($date) {
                    $query->where('date', $date);
                }
            });
        }

        $this->locationFilter($instructors, $filter, $result);

        $per_page = $data['per_page'] ?? 50;

        return $result->paginate($per_page);
    }

    private function locationFilter($instructors, $filter, &$result)
    {
        if (!isset($filter['lat']) && !isset($filter['lng'])) return;
        if (empty($filter['lat']) && empty($filter['lng'])) return;

        // 1. distance calculation and filtration
        $points = DB::table('drivisa__points')->where('deleted_at',null)
            ->select('drivisa__points.*', DB::raw("6371 * acos(cos(radians(" . $filter['lat'] . "))
             * cos(radians(source_latitude)) 
             * cos(radians(source_longitude) - radians(" . $filter['lng'] . ")) 
             + sin(radians(" . $filter['lat'] . ")) 
             * sin(radians(source_latitude))) AS distance"))
            ->having('distance', '<', $filter['within_km'] ?? 50000)
            ->whereIn('instructor_id', $instructors->pluck('id')->toArray())
            ->get();

        // result filter with points
        $result = $instructors->whereIn('drivisa__instructors.id', $points->pluck('instructor_id')->toArray());
    }
}
