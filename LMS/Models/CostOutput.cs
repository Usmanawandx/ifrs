//------------------------------------------------------------------------------
// <auto-generated>
//     This code was generated from a template.
//
//     Manual changes to this file may cause unexpected behavior in your application.
//     Manual changes to this file will be overwritten if the code is regenerated.
// </auto-generated>
//------------------------------------------------------------------------------

namespace LMS.Models
{
    using System;
    using System.Collections.Generic;
    
    public partial class CostOutput
    {
        public int id { get; set; }
        public string FacilityID { get; set; }
        public System.DateTime MonthOfCost { get; set; }
        public double CostAmount { get; set; }
        public Nullable<System.DateTime> NPA_Date { get; set; }
        public Nullable<double> InterestRate { get; set; }
        public Nullable<double> EconomicCost { get; set; }
        public string by_user { get; set; }
    }
}
