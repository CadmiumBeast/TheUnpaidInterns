<?php

namespace Database\Seeders;

use App\Models\Medicine;
use App\Models\Pharmacy;
use App\Models\PharmacyMedicine;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Phar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@example.com',
        //     'password' => 'password',
        //     'type' => 'admin',
        // ]);


        // $pharmacies = [
        //     ["name" => "Rajagiriya Osusala", "location" => "Rajagiriya", "phone_number" => "011-2865923"],
        //     ["name" => "Central Osusala - Colombo", "location" => "Colombo 10", "phone_number" => "011-2320353"],
        //     ["name" => "Anuradhapura Osusala", "location" => "Anuradhapura", "phone_number" => "025-2222230"],
        //     ["name" => "Badulla Osusala", "location" => "Badulla", "phone_number" => "055-2222918"],
        //     ["name" => "Batticaloa Osusala", "location" => "Batticaloa", "phone_number" => "065-2222268"],
        //     ["name" => "Galle Osusala", "location" => "Galle", "phone_number" => "091-2222780"],
        //     ["name" => "Gampaha Osusala", "location" => "Gampaha", "phone_number" => "033-2234695"],
        //     ["name" => "Hambantota Osusala", "location" => "Hambantota", "phone_number" => "047-2222606"],
        //     ["name" => "Jaffna Osusala", "location" => "Jaffna", "phone_number" => "021-2222145"],
        //     ["name" => "Kalutara Osusala", "location" => "Kalutara", "phone_number" => "034-2235337"],
        //     ["name" => "Kandy Osusala", "location" => "Kandy", "phone_number" => "081-2222264"],
        //     ["name" => "Kurunegala Osusala", "location" => "Kurunegala", "phone_number" => "037-2222264"],
        //     ["name" => "Mannar Osusala", "location" => "Mannar", "phone_number" => "023-2222265"],
        //     ["name" => "Matale Osusala", "location" => "Matale", "phone_number" => "066-2222263"],
        //     ["name" => "Matara Osusala", "location" => "Matara", "phone_number" => "041-2222662"],
        //     ["name" => "Monaragala Osusala", "location" => "Monaragala", "phone_number" => "055-2277226"],
        //     ["name" => "Nuwara Eliya Osusala", "location" => "Nuwara Eliya", "phone_number" => "052-2222262"],
        //     ["name" => "Polonnaruwa Osusala", "location" => "Polonnaruwa", "phone_number" => "027-2222261"],
        //     ["name" => "Ratnapura Osusala", "location" => "Ratnapura", "phone_number" => "045-2222260"],
        //     ["name" => "Trincomalee Osusala", "location" => "Trincomalee", "phone_number" => "026-2222259"],
        //     ["name" => "Vavuniya Osusala", "location" => "Vavuniya", "phone_number" => "024-2222258"],
        //     ["name" => "Chilaw Osusala", "location" => "Chilaw", "phone_number" => "032-2222257"],
        //     ["name" => "Negombo Osusala", "location" => "Negombo", "phone_number" => "031-2222256"],
        //     ["name" => "Panadura Osusala", "location" => "Panadura", "phone_number" => "038-2232255"],
        //     ["name" => "Avissawella Osusala", "location" => "Avissawella", "phone_number" => "036-2232254"],
        //     ["name" => "Embilipitiya Osusala", "location" => "Embilipitiya", "phone_number" => "047-2262253"],
        //     ["name" => "Puttalam Osusala", "location" => "Puttalam", "phone_number" => "032-2262252"],
        //     ["name" => "Bandarawela Osusala", "location" => "Bandarawela", "phone_number" => "057-2222251"],
        //     ["name" => "Kegalle Osusala", "location" => "Kegalle", "phone_number" => "035-2222250"],
        //     ["name" => "Ampara Osusala", "location" => "Ampara", "phone_number" => "063-2222249"],
        //     ["name" => "Kuliyapitiya Osusala", "location" => "Kuliyapitiya", "phone_number" => "037-2282248"],
        //     ["name" => "Kilinochchi Osusala", "location" => "Kilinochchi", "phone_number" => "021-2282247"],
        //     ["name" => "Mullaitivu Osusala", "location" => "Mullaitivu", "phone_number" => "021-2292246"],
        //     ["name" => "Deniyaya Osusala", "location" => "Deniyaya", "phone_number" => "041-2282245"],
        //     ["name" => "Akkaraipattu Osusala", "location" => "Akkaraipattu", "phone_number" => "067-2282244"],
        //     ["name" => "Kalmunai Osusala", "location" => "Kalmunai", "phone_number" => "067-2292243"],
        //     ["name" => "Kataragama Osusala", "location" => "Kataragama", "phone_number" => "047-2292242"],
        //     ["name" => "Horana Osusala", "location" => "Horana", "phone_number" => "034-2262241"],
        //     ["name" => "Wattegama Osusala", "location" => "Wattegama", "phone_number" => "081-2292240"],
        //     ["name" => "Hatton Osusala", "location" => "Hatton", "phone_number" => "051-2222239"],
        // ];

        // foreach ($pharmacies as $pharmacy) {
        //     Pharmacy::create($pharmacy);
        // }

            // // Medicines seeder
            // $medicines = [
            //     ["name" => "Paracetamol", "description" => "Pain reliever and fever reducer.", "price" => 2.50, "manufacturer" => "ABC Pharma", "type" => "Tablet", "dosage" => "500mg"],
            //     ["name" => "Paracetamol", "description" => "Pain reliever and fever reducer.", "price" => 1.50, "manufacturer" => "ABC Pharma", "type" => "Tablet", "dosage" => "250mg"],
            //     ["name" => "Ibuprofen", "description" => "Nonsteroidal anti-inflammatory drug.", "price" => 3.00, "manufacturer" => "XYZ Labs", "type" => "Tablet", "dosage" => "200mg"],
            //     ["name" => "Ibuprofen", "description" => "Nonsteroidal anti-inflammatory drug.", "price" => 4.00, "manufacturer" => "XYZ Labs", "type" => "Tablet", "dosage" => "400mg"],
            //     ["name" => "Amoxicillin", "description" => "Antibiotic for bacterial infections.", "price" => 5.00, "manufacturer" => "MediCure", "type" => "Capsule", "dosage" => "250mg"],
            //     ["name" => "Amoxicillin", "description" => "Antibiotic for bacterial infections.", "price" => 6.00, "manufacturer" => "MediCure", "type" => "Capsule", "dosage" => "500mg"],
            //     ["name" => "Ciprofloxacin", "description" => "Antibiotic for bacterial infections.", "price" => 7.00, "manufacturer" => "PharmaPlus", "type" => "Tablet", "dosage" => "500mg"],
            //     ["name" => "Ciprofloxacin", "description" => "Antibiotic for bacterial infections.", "price" => 4.50, "manufacturer" => "PharmaPlus", "type" => "Tablet", "dosage" => "250mg"],
            //     ["name" => "Metformin", "description" => "Diabetes medication.", "price" => 2.80, "manufacturer" => "DiabeCare", "type" => "Tablet", "dosage" => "500mg"],
            //     ["name" => "Metformin", "description" => "Diabetes medication.", "price" => 3.20, "manufacturer" => "DiabeCare", "type" => "Tablet", "dosage" => "850mg"],
            //     ["name" => "Aspirin", "description" => "Pain reliever and blood thinner.", "price" => 1.20, "manufacturer" => "HealthGen", "type" => "Tablet", "dosage" => "75mg"],
            //     ["name" => "Aspirin", "description" => "Pain reliever and blood thinner.", "price" => 2.00, "manufacturer" => "HealthGen", "type" => "Tablet", "dosage" => "300mg"],
            //     ["name" => "Atorvastatin", "description" => "Cholesterol lowering medication.", "price" => 8.00, "manufacturer" => "CardioPharm", "type" => "Tablet", "dosage" => "10mg"],
            //     ["name" => "Atorvastatin", "description" => "Cholesterol lowering medication.", "price" => 9.00, "manufacturer" => "CardioPharm", "type" => "Tablet", "dosage" => "20mg"],
            //     ["name" => "Simvastatin", "description" => "Cholesterol lowering medication.", "price" => 7.50, "manufacturer" => "CardioPharm", "type" => "Tablet", "dosage" => "10mg"],
            //     ["name" => "Simvastatin", "description" => "Cholesterol lowering medication.", "price" => 8.50, "manufacturer" => "CardioPharm", "type" => "Tablet", "dosage" => "20mg"],
            //     ["name" => "Omeprazole", "description" => "Acid reflux treatment.", "price" => 5.50, "manufacturer" => "GastroCare", "type" => "Capsule", "dosage" => "20mg"],
            //     ["name" => "Omeprazole", "description" => "Acid reflux treatment.", "price" => 6.50, "manufacturer" => "GastroCare", "type" => "Capsule", "dosage" => "40mg"],
            //     ["name" => "Lisinopril", "description" => "Blood pressure medication.", "price" => 3.80, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "10mg"],
            //     ["name" => "Lisinopril", "description" => "Blood pressure medication.", "price" => 4.80, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "20mg"],
            //     ["name" => "Losartan", "description" => "Blood pressure medication.", "price" => 5.20, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "50mg"],
            //     ["name" => "Losartan", "description" => "Blood pressure medication.", "price" => 6.20, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "100mg"],
            //     ["name" => "Azithromycin", "description" => "Antibiotic for bacterial infections.", "price" => 10.00, "manufacturer" => "MediCure", "type" => "Tablet", "dosage" => "250mg"],
            //     ["name" => "Azithromycin", "description" => "Antibiotic for bacterial infections.", "price" => 12.00, "manufacturer" => "MediCure", "type" => "Tablet", "dosage" => "500mg"],
            //     ["name" => "Doxycycline", "description" => "Antibiotic for bacterial infections.", "price" => 7.00, "manufacturer" => "PharmaPlus", "type" => "Capsule", "dosage" => "100mg"],
            //     ["name" => "Doxycycline", "description" => "Antibiotic for bacterial infections.", "price" => 8.00, "manufacturer" => "PharmaPlus", "type" => "Capsule", "dosage" => "200mg"],
            //     ["name" => "Cetirizine", "description" => "Allergy relief.", "price" => 2.50, "manufacturer" => "AllerGen", "type" => "Tablet", "dosage" => "10mg"],
            //     ["name" => "Cetirizine", "description" => "Allergy relief.", "price" => 1.80, "manufacturer" => "AllerGen", "type" => "Tablet", "dosage" => "5mg"],
            //     ["name" => "Loratadine", "description" => "Allergy relief.", "price" => 2.80, "manufacturer" => "AllerGen", "type" => "Tablet", "dosage" => "10mg"],
            //     ["name" => "Loratadine", "description" => "Allergy relief.", "price" => 1.90, "manufacturer" => "AllerGen", "type" => "Tablet", "dosage" => "5mg"],
            //     ["name" => "Ranitidine", "description" => "Acid reducer.", "price" => 3.00, "manufacturer" => "GastroCare", "type" => "Tablet", "dosage" => "150mg"],
            //     ["name" => "Ranitidine", "description" => "Acid reducer.", "price" => 4.00, "manufacturer" => "GastroCare", "type" => "Tablet", "dosage" => "300mg"],
            //     ["name" => "Pantoprazole", "description" => "Acid reflux treatment.", "price" => 5.00, "manufacturer" => "GastroCare", "type" => "Tablet", "dosage" => "20mg"],
            //     ["name" => "Pantoprazole", "description" => "Acid reflux treatment.", "price" => 6.00, "manufacturer" => "GastroCare", "type" => "Tablet", "dosage" => "40mg"],
            //     ["name" => "Clarithromycin", "description" => "Antibiotic for bacterial infections.", "price" => 9.00, "manufacturer" => "MediCure", "type" => "Tablet", "dosage" => "250mg"],
            //     ["name" => "Clarithromycin", "description" => "Antibiotic for bacterial infections.", "price" => 11.00, "manufacturer" => "MediCure", "type" => "Tablet", "dosage" => "500mg"],
            //     ["name" => "Hydrochlorothiazide", "description" => "Diuretic for blood pressure.", "price" => 2.20, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "12.5mg"],
            //     ["name" => "Hydrochlorothiazide", "description" => "Diuretic for blood pressure.", "price" => 2.80, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "25mg"],
            //     ["name" => "Amlodipine", "description" => "Blood pressure medication.", "price" => 3.50, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "5mg"],
            //     ["name" => "Amlodipine", "description" => "Blood pressure medication.", "price" => 4.50, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "10mg"],
            //     ["name" => "Gliclazide", "description" => "Diabetes medication.", "price" => 2.90, "manufacturer" => "DiabeCare", "type" => "Tablet", "dosage" => "40mg"],
            //     ["name" => "Gliclazide", "description" => "Diabetes medication.", "price" => 3.90, "manufacturer" => "DiabeCare", "type" => "Tablet", "dosage" => "80mg"],
            //     ["name" => "Warfarin", "description" => "Blood thinner.", "price" => 6.00, "manufacturer" => "HealthGen", "type" => "Tablet", "dosage" => "1mg"],
            //     ["name" => "Warfarin", "description" => "Blood thinner.", "price" => 7.00, "manufacturer" => "HealthGen", "type" => "Tablet", "dosage" => "5mg"],
            //     ["name" => "Prednisolone", "description" => "Steroid anti-inflammatory.", "price" => 2.60, "manufacturer" => "SteroidGen", "type" => "Tablet", "dosage" => "5mg"],
            //     ["name" => "Prednisolone", "description" => "Steroid anti-inflammatory.", "price" => 3.60, "manufacturer" => "SteroidGen", "type" => "Tablet", "dosage" => "10mg"],
            //     ["name" => "Furosemide", "description" => "Diuretic for fluid retention.", "price" => 2.10, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "20mg"],
            //     ["name" => "Furosemide", "description" => "Diuretic for fluid retention.", "price" => 2.90, "manufacturer" => "HyperMed", "type" => "Tablet", "dosage" => "40mg"],
            // ];

            // // Uncomment and adjust the following if you have a Medicine model:
            // foreach ($medicines as $medicine) {
            //     \App\Models\Medicine::create($medicine);
            // }
                

            // Get all pharmacies and medicines
        $pharmacies = Pharmacy::all();
        $medicines = Medicine::all();

        // Assign random medicines to each pharmacy with random stock
        foreach ($pharmacies as $pharmacy) {
            // Each pharmacy gets 10 random medicines
            $randomMedicines = $medicines->random(min(10, $medicines->count()));

            foreach ($randomMedicines as $medicine) {
                PharmacyMedicine::create([
                    'pharmacy_id' => $pharmacy->id,
                    'medicine_id' => $medicine->id,
                    'stock' => rand(10, 100),
                ]);
            }
        }
        
    }
}
