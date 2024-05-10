using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.ComponentModel;
using System.ComponentModel.DataAnnotations;

namespace LMS.Models
{
    public class FileModel
    {

        [DisplayName("Upload Your File")]
        public HttpPostedFileBase fileName { get; set; }
        public string mmpd { get; set; }

    }
    public class TwoFileModel
    {

        [DisplayName("Upload Your File")]
        public HttpPostedFileBase fileName { get; set; }
        [DisplayName("Upload Your File")]
        public HttpPostedFileBase fileName2 { get; set; }

    }
}