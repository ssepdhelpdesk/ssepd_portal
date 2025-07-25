<?php

namespace App\Imports;

use App\Models\NgoRegistration;
use App\Models\NgoPartFiveListOfBeneficiary;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;

class NgoPartFiveListOfBeneficiaryImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    protected $ngo;

    public function __construct(NgoRegistration $ngo)
    {
        $this->ngo = $ngo;
    }

    public function model(array $row)
    {
        $dob = $this->parseDate($row['date_of_birth']);
        $association = $this->parseDate($row['date_of_association']);

        return new NgoPartFiveListOfBeneficiary([
            'ngo_org_id'             => $this->ngo->ngo_org_id,
            'ngo_tbl_id'             => $this->ngo->id,
            'ngo_system_gen_reg_no'  => $this->ngo->ngo_system_gen_reg_no,
            'beneficiary_name'       => $row['beneficiary_name'],
            'gender'                 => $row['gender'],
            'date_of_birth'          => $dob,
            'qualification'          => $row['qualification'],
            'date_of_association'    => $association,
            'aadhar_number'          => $row['aadhar_number'],
            'mobile_no'              => $row['mobile_no'],
            'created_date'           => now()->setTimezone('Asia/Kolkata')->toDateString(),
            'created_time'           => now()->setTimezone('Asia/Kolkata')->toTimeString(),
            'created_by'             => Auth::id(),
            'status'                 => 1,
        ]);
    }

    public function rules(): array
    {
        return [
            'beneficiary_name'    => 'required|string|max:255',
            'gender'              => 'required|in:Male,Female,Other',
            'date_of_birth'       => ['required', function ($attribute, $value, $fail) {
                $dob = $this->parseDate($value);
                if (!$dob) {
                    return $fail('Date of Birth must be a valid date in DD-MM-YYYY format.');
                }
                if ($dob->isFuture()) {
                    return $fail('Date of Birth cannot be in the future.');
                }
            }],
            'qualification'       => 'required|string|max:255',
            'date_of_association' => ['required', function ($attribute, $value, $fail) {
                $association = $this->parseDate($value);
                $dobValue = request()->input('date_of_birth');

                $dob = $this->parseDate($dobValue);

                if (!$association) {
                    return $fail('Date of Association must be a valid date in DD-MM-YYYY format.');
                }
                if ($association->isFuture()) {
                    return $fail('Date of Association cannot be in the future.');
                }
                if ($dob && $dob->equalTo($association)) {
                    return $fail('Date of Birth and Date of Association cannot be the same.');
                }
                if ($dob && $dob->greaterThan($association)) {
                    return $fail('Date of Birth must be before Date of Association.');
                }
            }],
            'aadhar_number'       => 'required|digits:12|unique:ngo_part_five_list_of_beneficiaries,aadhar_number',
            'mobile_no'           => 'required|digits:10',
        ];
    }

    protected function parseDate($value)
    {
        try {
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value))->startOfDay();
            } else {
                return Carbon::createFromFormat('d-m-Y', $value)->startOfDay();
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function customValidationMessages()
    {
        return [
            'beneficiary_name.required'    => 'Beneficiary name is required.',
            'gender.required'              => 'Gender is required.',
            'gender.in'                    => 'Gender must be one of: Male, Female, or Other.',
            'date_of_birth.required'       => 'Date of Birth is required.',
            'qualification.required'       => 'Qualification is required.',
            'date_of_association.required' => 'Date of Association is required.',
            'aadhar_number.required'       => 'Aadhar number is required.',
            'aadhar_number.digits'         => 'Aadhar number must be exactly 12 digits.',
            'aadhar_number.unique'         => 'This Aadhar number already exists in the database.',
            'mobile_no.required'           => 'Mobile number is required.',
            'mobile_no.digits'             => 'Mobile number must be exactly 10 digits.',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'beneficiary_name'    => 'Beneficiary Name',
            'gender'              => 'Gender',
            'date_of_birth'       => 'Date of Birth',
            'qualification'       => 'Qualification',
            'date_of_association' => 'Date of Association',
            'aadhar_number'       => 'Aadhar Number',
            'mobile_no'           => 'Mobile Number',
        ];
    }
}
